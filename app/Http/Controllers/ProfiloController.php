<?php

use Illuminate\Routing\Controller as BaseController;

class ProfiloController extends BaseController{

    public function profilo(){
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
        if($user->impiegato==1){
            //select azienda.nome from azienda join users on impiego=azienda.id where users.id=".$_SESSION['id'];
            $IDimpiego=User::find(session('id'))->impiego;
            $impiego=Azienda::find($IDimpiego)->nome;
            //select id, nome from azienda where id!=(select impiego from users where id=".$_SESSION['id'].")
            $elementiForm=Azienda::where('id','!=',$IDimpiego)->select('id','nome')->get();
            //select nome,ip.dataAssunzione,fineImpiego from impiegopassato ip 
            //join azienda on impiegopassato=azienda.id where user=".$_SESSION['id']." order by ip.dataAssunzione";
            $impieghiPassati=ImpiegoPassato::where('user',session('id'))->orderBy('dataAssunzione')->get();
            foreach($impieghiPassati as $impiegoPassato){
                $impiegoPassato['nome']=Azienda::find($impiegoPassato['impiegoPassato'])->nome;
            }
                return view('profilo')
                ->with('app_url', env('APP_URL'))
                ->with('app_folder', env('APP_FOLDER'))
                ->with('username',$user->username)
                ->with('nome', $user->nome)
                ->with('cognome', $user->cognome)
                ->with('dataRegistrazione', $user->dataRegistrazione)
                ->with('stipendio',$user->stipendio)
                ->with('impiego', $impiego)
                ->with('categorieSide',$categorieSide)
                ->with('dataAssunzione',$user->dataAssunzione)
                ->with('elementiForm', $elementiForm)
                ->with('impieghiPassati', $impieghiPassati)
                ->with('propicURL',$propicURL)
                ->with('csrf_token', csrf_token())
                ->with('countCarrello',$user->numCarrello);
        }else{
            return view('profilo')
            ->with('app_url', env('APP_URL'))
            ->with('app_folder', env('APP_FOLDER'))
            ->with('username',$user->username)
            ->with('nome', $user->nome)
            ->with('cognome', $user->cognome)
            ->with('categorieSide',$categorieSide)
            ->with('dataRegistrazione', $user->dataRegistrazione)
            ->with('nascondi', "class=hidden")
            ->with('propicURL',$propicURL)
            ->with('csrf_token', csrf_token())
            ->with('countCarrello',$user->numCarrello);
        }
        
    }
    public function cambiaImmagine(){
        if(isset($_FILES['image'])){
            if($_FILES['image']['error']===0){
                    if($_FILES['image']['size']<2000000){
                    $type = exif_imagetype($_FILES['image']['tmp_name']);//ottengo il tipo dell'immagine
                    $allowedExt = array(IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_GIF => 'gif');//faccio un'array con chiave=tipo immagine, valore=corrispettiva estensione
                    if(isset($allowedExt[$type])){//se il tipo dell'immagine risulta essere una chiave dell'array precedente vuol dire che è di un tipo accettato
                        $fileName = uniqid('', true).".".$allowedExt[$type];//genero un id per il nome del file e metto l'estensione corretta
                        move_uploaded_file($_FILES['image']['tmp_name'], "uploads".DIRECTORY_SEPARATOR."$fileName");//sposto il file nella cartella uploads
                        //update users set propic='$fileName' where id=".$_SESSION['id']
                        $user=User::find(session('id'));
                        $user->propic=$fileName;
                        $user->save();
                        $result["fileName"]=$fileName;//restituisco il nome della nuova immagine così da poterla cambiare tramite js
                    }else{
                        $result['errors'][]="Inserisci un formato valido (jpeg, jpg, png o gif)";
                    }
                } else {
                    $result['errors'][]="L'immagine non può avere dimensioni superiori a 2MB";
                }
            } else $result['errors'][]="Inserisci un'immagine";
        }
        return $result;
    }

    public function cambiaImpiego(){
        //update users set impiego=$impiego, dataAssunzione=current_timestamp where id=".$_SESSION['id']
        $user=User::find(session('id'));
        $user->impiego=request('impiego');
        $user->dataAssunzione=date("Y-m-d G:i:s", strtotime("+2 hour"));
        $user->save();
        //select id, nome from azienda where id!=(select impiego from users where id=".$_SESSION['id'].")
        $IDimpiego=User::find(session('id'))->impiego;
        $elementiForm=Azienda::where('id','!=',$IDimpiego)->select('id','nome')->get();
        foreach($elementiForm as $elementoForm){
            $result['elementiForm'][]=$elementoForm;
        }
        //select nome,ip.dataAssunzione,fineImpiego from impiegopassato ip 
        //join azienda a on ip.impiegopassato=a.id where user=".$_SESSION['id']." order by ip.dataAssunzione
        $impieghiPassati=ImpiegoPassato::where('user',session('id'))->orderBy('dataAssunzione')->get();
        foreach($impieghiPassati as $impiegoPassato){
            $impiegoPassato['nome']=Azienda::find($impiegoPassato['impiegoPassato'])->nome;
        }
        $result['impieghiPassati']=$impieghiPassati;
        //select a.nome, u.dataAssunzione from azienda a join users u on u.impiego=a.id where u.id=".$_SESSION['id']
        $result['impiego']=array("nome"=>Azienda::find($IDimpiego)->nome,"dataAssunzione"=>User::find(session('id'))->dataAssunzione);
        return $result;
    }
    public function fetchWishlist(){
        //select titolo as prodotto,immagine from utente_prodotto join prodotto as p on p.id=prodotto 
        //where user=".$_SESSION['id']." and wishlist=true order by prodotto
        $prodotti=User::find(session('id'))->utente_prodotto()->orderBy('titolo')->get();
        $wishlist=[];
        foreach($prodotti as $prodotto){
            $row=UtenteProdotto::where('user',session('id'))->where('prodotto',$prodotto['id'])->first();
            if($row->wishlist==1){
                $prodotto['wishlist']=true;
                $wishlist[]=$prodotto;
            }
        }
        return $wishlist;
    }
    public function fetchRecensioniProfilo(){
        //select r.id,titolo,r.descrizione,voto,data,numLike, lr.user as youLike from prodotto p join 
        //recensioni r on p.id=r.prodotto left join like_recensioni lr on 
        //(lr.user=".$_SESSION['id']." and lr.recensione=r.id) where r.user=".$_SESSION['id']
        $prodotti=User::find(session('id'))->recensioni;
        $recensioni=[];
        if($prodotti){
            foreach($prodotti as $prodotto){
                $info=Recensione::where('prodotto',$prodotto->id)->where('user',session('id'))->first();
                $info['titolo']=$prodotto->titolo;
                $row=LikeRecensioni::where('user',session('id'))->where('recensione',$info['id'])->first();
                if(isset($row)){
                    $info["youLike"]=true;
                }
                $recensioni[]=$info;
            }
        }
        return $recensioni;
    }
    public function fetchAcquisti(){
        //select titolo,immagine,acquisto as quantita from utente_prodotto up 
        //join prodotto p on prodotto=p.id where acquisto>0 and user=".$_SESSION['id'];
        $prodotti=User::find(session('id'))->utente_prodotto;
        $acquisti=[];
        foreach($prodotti as $prodotto){
            $row=UtenteProdotto::where('user',session('id'))->where('prodotto',$prodotto['id'])->first();
            if($row->acquisto>0){
                $prodotto['quantita']=$row->acquisto;
                $acquisti[]=$prodotto;
            }
        }
        return $acquisti;
    }

    public function eliminaRecensione($id){
        //delete from recensioni where id=$id";
        $recensione=Recensione::find($id);
        $recensione->delete();
    }
}
