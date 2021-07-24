<?php

use Illuminate\Routing\Controller as BaseController;

class SideNavController extends BaseController{

    public function fetchProdottiSide($titolo){
        //select titolo,immagine,prezzo,wishlist,disponibilita,inArrivo from prodotto p 
        //left join utente_prodotto on (prodotto=p.id and user='".$_SESSION['id']."') where titolo like '%$titolo%'"
        $parametri=explode(" ",request('ordine'));
        if(count($parametri)>1){
            if(request('categoria')==="Qualsiasi") $prodotti=Prodotto::where('titolo','like',"%$titolo%")->orderBy($parametri[0],$parametri[1])->get();
            else $prodotti=Prodotto::where('titolo','like',"%$titolo%")->where('categoria',request('categoria'))->orderBy($parametri[0],$parametri[1])->get();
        } else {
            if(request('categoria')==="Qualsiasi") $prodotti=Prodotto::where('titolo','like',"%$titolo%")->orderBy($parametri[0])->get();
            else $prodotti=Prodotto::where('titolo','like',"%$titolo%")->where('categoria',request('categoria'))->orderBy($parametri[0])->get();
        }
        
        $prodottiUtente=UtenteProdotto::where('user',session('id'))->where('wishlist',true)->get();
        foreach($prodotti as $prodotto){
            foreach($prodottiUtente as $prodottoUtente){
                if($prodotto['id']===$prodottoUtente['prodotto']){
                    $prodotto['wishlist']=true;
                }
            }
        }
        return $prodotti;
    }
    
    public function fetchUtenti($username){
        if(session('tipo')==1){
            //select username,propic,a.nome as impiego from users u 
            //left join azienda a on a.id=u.impiego where username like '%$username%' and u.id!=".$_SESSION['id']
            $utenti=User::where('username','like',"%$username%")->where('id','!=',session('id'))->select('username','propic','impiego')->get();
            foreach($utenti as $utente){
                if($utente->impiego) $utente->impiego=Azienda::find($utente->impiego)->nome;
            }
        }else {
            //select username,propic from users where username like '%$username%' and id!=".$_SESSION['id'];
            $utenti=User::where('username','like',"%$username%")->where('id','!=',session('id'))->select('username','propic')->get();
        }
        return $utenti;
    }
    
}
