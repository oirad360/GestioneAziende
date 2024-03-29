<?php

use Illuminate\Routing\Controller as BaseController;

class HomeController extends BaseController{

    public function home(){
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
        return view('home')
            ->with('app_url', env('APP_URL'))
            ->with('app_folder', env('APP_FOLDER'))
            ->with('username',$user->username)
            ->with('csrf_token', csrf_token())
            ->with('propicURL',$propicURL)
            ->with('categorieSide',$categorieSide)
            ->with('countCarrello',$user->numCarrello);
    }
    public function fetchAziende(){
        $aziende=Azienda::all();
        return $aziende;
    }
}
