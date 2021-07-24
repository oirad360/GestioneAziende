<?php

use Illuminate\Routing\Controller as BaseController;

class ProdottiController extends BaseController{

    public function prodotti($azienda){
        if(session('id')===null){
            return redirect('login');
        }
        $user=User::find(session('id'));
        if($user->propic==="defaultAvatar.jpg"){
            $propicURL="/".env('APP_FOLDER')."/public/assets/defaultAvatar.jpg";
        } else {
            $propicURL="/".env('APP_FOLDER')."/public/uploads/".$user->propic;
        }
        $categorieSide=Prodotto::select('categoria')->distinct()->get();
        $categorie=Azienda::where('nome',$azienda)->first()->prodotti()->select('categoria')->distinct()->get();
        return view('prodotti')
            ->with('app_url', env('APP_URL'))
            ->with('app_folder', env('APP_FOLDER'))
            ->with('csrf_token', csrf_token())
            ->with('username',$user->username)
            ->with('categorie',$categorie)
            ->with('categorieSide',$categorieSide)
            ->with('propicURL',$propicURL)
            ->with('countCarrello',$user->numCarrello)
            ->with('azienda',$azienda)
            ->with('aziendaUpperCase', strtoupper($azienda));
    }

    public function fetchProdotti($azienda){
        //select titolo,immagine,prezzo,wishlist,descrizione,disponibilita,inArrivo from azienda join prodotto on azienda.id=produttore 
        //left join utente_prodotto on (prodotto.id=prodotto and user='".$_SESSION['id']."') where azienda.nome='$azienda'
        $azienda=Azienda::where('nome',$azienda)->first();
        $parametri=explode(" ",request('ordine'));
        if(count($parametri)>1){
            $prodottiAzienda=$azienda->prodotti()->orderBy($parametri[0],$parametri[1])->get();
        } else {
            $prodottiAzienda=$azienda->prodotti()->orderBy($parametri[0])->get();
        }
        
        $prodottiUtente=UtenteProdotto::where('user',session('id'))->where('wishlist',true)->get();
        foreach($prodottiAzienda as $prodottoAzienda){
            if($prodottoAzienda["categoria"]===request('categoria') || request('categoria')==="Qualsiasi") $prodottoAzienda['mostra']=true;
            foreach($prodottiUtente as $prodottoUtente){
                if($prodottoAzienda['id']===$prodottoUtente['prodotto']){
                    $prodottoAzienda['wishlist']=true;
                }
            }
        }
        return $prodottiAzienda;
    }

    public function aggiungiWishlist($titolo){
        //select 1 from utente_prodotto where user=".$_SESSION['id']." and prodotto=(select id from prodotto where titolo='$titolo')";
        $id=Prodotto::where('titolo',$titolo)->first()->id;
        $row=UtenteProdotto::where('user',session('id'))->where('prodotto',$id)->first();
        if(isset($row)){
            //update utente_prodotto set wishlist=true where user=".$_SESSION['id']." 
            //and prodotto=(select id from prodotto where titolo='$titolo')
            $row->wishlist=true;
            $row->save();
        } else {
            //insert into utente_prodotto(user,prodotto,wishlist)
            //values(".$_SESSION['id'].",(select id from prodotto where titolo='$titolo'),true)
            $row=new UtenteProdotto;
            $row->user=session('id');
            $row->prodotto=$id;
            $row->wishlist=true;
            $row->save();
        }
    }
    public function rimuoviWishlist($titolo){
        //update utente_prodotto set wishlist=false 
        //where user=".$_SESSION['id']." and prodotto=(select id from prodotto where titolo='$titolo')
        $id=Prodotto::where('titolo',$titolo)->first()->id;
        $row=UtenteProdotto::where('user',session('id'))->where('prodotto',$id)->first();
        $row->wishlist=false;
        $row->save();
        //delete from utente_prodotto where user=".$_SESSION['id']." 
        //and prodotto=(select id from prodotto where titolo='$titolo') and wishlist=false and carrello=0 and acquisto=0
        $row=UtenteProdotto::where('user',session('id'))->where('prodotto', $id)->where('wishlist',false)->where('carrello',0)->where('acquisto',0)->first();
        if(isset($row)) $row->delete();
    }
    
    public function creativeCommons_api($titoloProdotto){
        $dati=array("client_id"=>env('CC_CLIENT_ID'),"client_secret"=>env('CC_CLIENT_SECRET'),"grant_type"=>"client_credentials");
        $dati=http_build_query($dati);
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_URL,"https://api.creativecommons.engineering/v1/auth_tokens/token/");
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$dati);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $token=curl_exec($curl);//ottengo il token
        curl_close($curl);
        $suggerimenti=Prodotto::where('titolo',$titoloProdotto)->first()->suggerimenti()->select('indexSuggerimento')->get();
        if(count($suggerimenti)>0){
            $searchTitle=Prodotto::where('titolo',$titoloProdotto)->first()->searchTitle;
            $dati=array("page_size"=>70,"title"=>$searchTitle);//preparo i parametri per la richiesta
            $dati=http_build_query($dati);
            $curl=curl_init();
            curl_setopt($curl,CURLOPT_URL,"https://api.creativecommons.engineering/v1/images?".$dati);//preparo l'url della richiesta
            $headers=array($token);//preparo l'header inserendo il token
            curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
            $resultRicerca=json_decode(curl_exec($curl));//ottengo i risultati (li decodifico perchè voglio restituire un risultato personalizzato da me)
            curl_close($curl);
            $i=0;
            foreach($resultRicerca as $value){ //faccio questo perchè $resultRicerca è un std Object con vari campi, a me interessa solo il quarto campo (results) 
                                                //sottoforma di array quindi lo estraggo scorrendo l'oggetto con questo foreach
                if($i===3)$arrayRicerca=$value;
                $i++;
            }
            foreach($suggerimenti as $suggerimento){//adesso estraggo gli indici ottenuti alla riga 15 e li uso per estrarre i risultati da $arrayRicerca e li inserisco nel mio array
                $result['contents'][]=$arrayRicerca[$suggerimento["indexSuggerimento"]];
            }
            $result['prodotto']=$titoloProdotto;
        } else {//se non sono presenti indici dò errore (significa che non ci sono suggerimenti)
            $result["contents"]=-1;
            $result["prodotto"]=$titoloProdotto;
        }
        return $result;
    }
    
}
