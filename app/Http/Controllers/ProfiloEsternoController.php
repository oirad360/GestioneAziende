<?php

use Illuminate\Routing\Controller as BaseController;

class ProfiloEsternoController extends BaseController{

    public function profilo($username){
        if(session('id')===null){
            return redirect('login');
        }
        if(session('username')===$username){
            return redirect("/profilo");
        }
        $user=User::find(session('id'));
        if($user->propic==="defaultAvatar.jpg"){
            $propicURL="/".env('APP_FOLDER')."/public/assets/defaultAvatar.jpg";
        } else {
            $propicURL="/".env('APP_FOLDER')."/public/uploads/".$user->propic;
        }
        $profilo=User::where('username',$username)->first();
        $recensioni=User::where('username',$username)->first()->recensioni()->get();
        $categorieSide=Prodotto::select('categoria')->distinct()->get();
        if($user->impiegato==1 && $profilo->impiegato==1){
            //select azienda.nome from azienda join users on impiego=azienda.id where users.id=".$_SESSION['id'];
            $IDimpiego=User::where('username',$username)->first()->impiego;
            $impiego=Azienda::find($IDimpiego)->nome;
            //select nome,ip.dataAssunzione,fineImpiego from impiegopassato ip 
            //join azienda on impiegopassato=azienda.id where user=".$_SESSION['id']." order by ip.dataAssunzione";
            $IDprofilo=User::where('username',$username)->first()->id;
            $impieghiPassati=ImpiegoPassato::where('user',$IDprofilo)->get();
            foreach($impieghiPassati as $impiegoPassato){
                $impiegoPassato['nome']=Azienda::find($impiegoPassato['impiegoPassato'])->nome;
            }
                return view('profiloEsterno')
                ->with('app_url', env('APP_URL'))
                ->with('app_folder', env('APP_FOLDER'))
                ->with('username',$user->username)
                ->with('usernameProfilo',$username)
                ->with('nome', $profilo->nome)
                ->with('cognome', $profilo->cognome)
                ->with('recensioni',$recensioni)
                ->with('categorieSide',$categorieSide)
                ->with('dataRegistrazione', $profilo->dataRegistrazione)
                ->with('impiego', $impiego)
                ->with('dataAssunzione',$profilo->dataAssunzione)
                ->with('impieghiPassati', $impieghiPassati)
                ->with('propicURL',$propicURL)
                ->with('csrf_token', csrf_token())
                ->with('countCarrello',$user->numCarrello);

        }else{
            return view('profiloEsterno')
            ->with('app_url', env('APP_URL'))
            ->with('app_folder', env('APP_FOLDER'))
            ->with('username',$user->username)
            ->with('usernameProfilo',$username)
            ->with('categorieSide',$categorieSide)
            ->with('nome', $profilo->nome)
            ->with('cognome', $profilo->cognome)
            ->with('recensioni',$recensioni)
            ->with('dataRegistrazione', $profilo->dataRegistrazione)
            ->with('nascondi', "class=hidden")
            ->with('propicURL',$propicURL)
            ->with('csrf_token', csrf_token())
            ->with('countCarrello',$user->numCarrello);
        }
        
    }

    public function fetchRecensioniProfiloEsterno($username){
        //select r.id,titolo,r.descrizione,voto,data,numLike, lr.user as youLike from prodotto p join recensioni r on p.id=r.prodotto 
        //left join like_recensioni lr on (lr.user=".$_SESSION['id']." and lr.recensione=r.id) where r.user=(select id from users where username='$username')";
        $prodotti=User::where('username',$username)->first()->recensioni;
        $id=User::where('username',$username)->first()->id;
        $recensioni=[];
        if($prodotti){
            foreach($prodotti as $prodotto){
                $info=Recensione::where('prodotto',$prodotto->id)->where('user',$id)->first();
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

}
