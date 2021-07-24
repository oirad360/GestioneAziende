<?php

use Illuminate\Routing\Controller as BaseController;

class RecensioniController extends BaseController{

    public function recensioni($titolo){
        if(session('id')===null){
            return redirect('login');
        }

        $user=User::find(session('id'));
        if($user->propic==="defaultAvatar.jpg"){
            $propicURL="/".env('APP_FOLDER')."/public/assets/defaultAvatar.jpg";
        } else {
            $propicURL="/".env('APP_FOLDER')."/public/uploads/".$user->propic;
        }
        //select 1 from recensioni where user=".$_SESSION['id']." and prodotto=(select id from prodotto where titolo='".$_GET['titolo']
        $id=Prodotto::where('titolo',$titolo)->first()->id;
        $miaRecensione=Recensione::where('user',session('id'))->where('prodotto',$id)->first();
        $categorieSide=Prodotto::select('categoria')->distinct()->get();
            return view('recensioni')
            ->with('csrf_token', csrf_token())
            ->with('app_url', env('APP_URL'))
            ->with('app_folder', env('APP_FOLDER'))
            ->with('username',$user->username)
            ->with('propicURL',$propicURL)
            ->with('categorieSide',$categorieSide)
            ->with('prodotto',$titolo)
            ->with('miaRecensione',$miaRecensione)
            ->with('countCarrello',$user->numCarrello);
    }
    public function fetchProdottoRecensioni($titolo){
        //select titolo,immagine,prezzo,descrizione,wishlist,disponibilita,inArrivo from prodotto p 
        //left join utente_prodotto on (prodotto=p.id and user='".$_SESSION['id']."') where titolo='$titolo'
        $prodotto=Prodotto::where('titolo',$titolo)->first();
        $prodottoUtente=UtenteProdotto::where('user',session('id'))->where('prodotto',$prodotto->id)->first();
        if(isset($prodottoUtente) && $prodottoUtente->wishlist==1){
            $prodotto['wishlist']=true;
        } else {
            $prodotto['wishlist']=false;
        }
        return $prodotto;
    }

    public function fetchRecensioni($titolo){
        //select r.id,propic, username, r.descrizione,voto,data,numLike, lr.user as youLike from recensioni r 
        //join users u on u.id=r.user left join like_recensioni lr on (lr.user=".$_SESSION['id']." and lr.recensione=r.id) 
        //where r.prodotto=(select id from prodotto where titolo='$titolo')";
        $id=Prodotto::where('titolo',$titolo)->first()->id;
        $utentiRecensione=Prodotto::where('titolo',$titolo)->first()->recensioni;
        $recensioni=array("contents"=>[],"disattivaRecensione"=>false);
        if($utentiRecensione){
            foreach($utentiRecensione as $utenteRecensione){
                $info=Recensione::where('prodotto',$id)->where('user',$utenteRecensione->id)->first();
                $info["propic"]=$utenteRecensione->propic;
                $info["username"]=$utenteRecensione->username;
                $row=LikeRecensioni::where('user',session('id'))->where('recensione',$info['id'])->first();
                if(isset($row)){
                    $info["youLike"]=true;
                }
                if(session('tipo')==1 && isset($utenteRecensione->impiego)){
                    $info["impiego"]=Azienda::find($utenteRecensione->impiego)->nome;
                }
                $recensioni["contents"][]=$info;
                if($utenteRecensione->id===session('id')){
                    $recensioni["disattivaRecensione"]=true;
                }
            }
        }
        return $recensioni;
    }

    public function pubblicaRecensione($titolo){
        //insert into recensioni(user,prodotto,descrizione,voto) 
        //values(".$_SESSION['id'].",(select id from prodotto where titolo='$titolo'),'$testoRecensione',$voto)"
        $recensione=new Recensione;
        $recensione->user=session('id');
        $recensione->prodotto=Prodotto::where('titolo',$titolo)->first()->id;
        $recensione->descrizione=request('testoRecensione');
        $recensione->voto=request('voto');
        $recensione->save();
    }

    public function like($id){
        //insert into like_recensioni(user,recensione) values(".$_SESSION['id'].",$id)";
        $like=new LikeRecensioni;
        $like->user=session('id');
        $like->recensione=$id;
        $like->save();
    }

    public function dislike($id){
        //insert into like_recensioni(user,recensione) values(".$_SESSION['id'].",$id)";
        $like=LikeRecensioni::where('recensione',$id)->where('user',session('id'))->first();
        $like->delete();
    }

    public function fetchUtentiLike($id){
        //select username,propic,a.nome as impiego from users u left join azienda a on a.id=u.impiego join 
        //like_recensioni lr on lr.user=u.id where lr.recensione=$id";
        $utenti=Recensione::find($id)->like()->select('username','propic','impiegato','users.id as id')->get();
        if(session('tipo')==1){
            foreach($utenti as $utente){
                if($utente->impiegato){
                    $IDimpiego=User::find($utente->id)->impiego;
                    $utente["impiego"]=Azienda::find($IDimpiego)->nome;
                }
            }
        }
        return $utenti;
    }
}
