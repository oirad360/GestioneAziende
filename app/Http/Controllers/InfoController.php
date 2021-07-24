<?php

use Illuminate\Routing\Controller as BaseController;

class InfoController extends BaseController{

    public function info(){
        if(session('id')===null){
            return redirect('login');
        }
        if(session('tipo')==0){
            $user=User::find(session('id'));
            if($user->propic==="defaultAvatar.jpg"){
                $propicURL="/".env('APP_FOLDER')."/public/assets/defaultAvatar.jpg";
            } else {
                $propicURL="/".env('APP_FOLDER')."/public/uploads/".$user->propic;
            }
            $categorieSide=Prodotto::select('categoria')->distinct()->get();
            return view('forbidden')
            ->with('app_url', env('APP_URL'))
            ->with('app_folder', env('APP_FOLDER'))
            ->with('csrf_token', csrf_token())
            ->with('username',$user->username)
            ->with('categorieSide',$categorieSide)
            ->with('propicURL',$propicURL)
            ->with('countCarrello',$user->numCarrello);
        }

        $user=User::find(session('id'));
        if($user->propic==="defaultAvatar.jpg"){
            $propicURL="/".env('APP_FOLDER')."/public/assets/defaultAvatar.jpg";
        } else {
            $propicURL="/".env('APP_FOLDER')."/public/uploads/".$user->propic;
        }
        $elementiForm=Azienda::select('id','nome')->get();
        $categorieSide=Prodotto::select('categoria')->distinct()->get();
        return view('info')
            ->with('app_url', env('APP_URL'))
            ->with('app_folder', env('APP_FOLDER'))
            ->with('csrf_token', csrf_token())
            ->with('username',$user->username)
            ->with('elementiForm',$elementiForm)
            ->with('categorieSide',$categorieSide)
            ->with('propicURL',$propicURL)
            ->with('countCarrello',$user->numCarrello);
    }
    public function fetchImpiegati(){
        //select username,propic,a.nome as impiego from users u 
        //join azienda a on a.id=u.impiego where impiego='".$azienda."'";
        $impiegati=Azienda::find(request('aziende'))->impiegati()->select('username','propic','impiego')->get();
        if(count($impiegati)>0){
            foreach($impiegati as $impiegato){
                $impiegato->impiego=Azienda::find(request('aziende'))->nome;
            }
        }
        return $impiegati;
    }

    public function quandl_api(){
        $parametri=array("collapse"=>request('collapse'),"end_date"=>request('end_date'),"start_date"=>request('start_date'),"api_key"=>env('QUANDL_KEY'));
        $parametri=http_build_query($parametri);
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_URL,"https://www.quandl.com/api/v3/datasets/".request('database_dataset').".json?".$parametri);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $result=curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}
