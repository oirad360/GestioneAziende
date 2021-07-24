<?php

use Illuminate\Routing\Controller as BaseController;

class SignupController extends BaseController{
    public function signup(){   
        $aziende=Azienda::all();
        $impieghi=array(array("value"=>"","nome"=>""));
        $i=0;//recupero tutti gli impieghi da mettere nel form
        foreach($aziende as $azienda){
            $impieghi[$i]["value"]=$azienda->id;
            $impieghi[$i]["nome"]=$azienda->nome;
            $i++;
        }
        
        return view('signup')
            ->with('app_url', env('APP_URL'))
            ->with('app_folder', env('APP_FOLDER'))
            ->with('old_assunzione', date("Y-m-d"))
            ->with('impieghi', $impieghi)
            ->with('checkCliente', "checked")
            ->with('checkImpiegato', "")
            ->with('csrf_token', csrf_token());
    }

    public function checkSignup(){
        if(request('tipo')==="cliente"){
            $checkCliente="checked";
            $checkImpiegato="";
        } else {
            $checkCliente="";
            $checkImpiegato="checked";
        }
        $errors=array();
        $aziende=Azienda::all();
        $impieghi=array(array("value"=>"","nome"=>""));
        $i=0;
        foreach($aziende as $azienda){
            $impieghi[$i]["value"]=$azienda->id;
            $impieghi[$i]["nome"]=$azienda->nome;
            $i++;
        }
        // se ho mandato dati post nulli (anche uno solo fra tutti) torno la vista con errore
        if(request('nome')===null || request('cognome')===null || request('username')===null || request('email')===null || request('password')===null || request('confermaPass')===null){
            $errors[]="Compila tutti i campi.";
            return view('signup')
                ->with('app_url', env('APP_URL'))
                ->with('app_folder', env('APP_FOLDER'))
                ->with('csrf_token', csrf_token())
                ->with('old_name', request('nome'))
                ->with('old_surname', request('cognome'))
                ->with('old_username', request('username'))
                ->with('old_email', request('email'))
                ->with('checkCliente', $checkCliente)
                ->with('checkImpiegato', $checkImpiegato)
                ->with('impieghi',$impieghi)
                ->with('old_assunzione', request('dataAssunzione'))
                ->with('old_stipendio', request('stipendio'))
                ->with('errors', $errors);
        }
        //se non ho fatto return allora ho mandato dei dati post non nulli, verifico la validità
        if(!ctype_alpha(request('nome'))) {
            $errors[]="Il nome non può contenere numeri o simboli.";
        }

        if(!ctype_alpha(request('cognome'))) {
            $errors[]="Il cognome non può contenere numeri o simboli.";
        }
        
        if(!ctype_alnum(request('username'))) {
            $errors[]="L'username può contenere solo caratteri alfanumerici.";
        } else {
            $user=User::where('username', request('username'))->first();
            if(isset($user)){
                $errors[]="Username già in uso.";
            }
        }
        
        if(!filter_var(request('email'), FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email non valida.";
        } else {
            $user=User::where('email', request('email'))->first();
            if(isset($user)){
                $errors[]="Email già in uso.";
            }
        }
        
        $password_error=false;
        if(!preg_match("#[0-9]+#", request('password'))) {
            $errors[] = "La password deve includere almeno un numero.";
            $password_error=true;
        }
        
        if(!preg_match("#[a-zA-Z]+#", request('password'))) {
            $errors[] = "La password deve includere almeno una lettera.";
            $password_error=true;
        }
        
        if(strlen(request('password'))<8){
            $errors[] = "La password deve avere almeno 8 caratteri.";
            $password_error=true;
        }

        if(request('password')!==request('confermaPass')){
            $errors[] = "Le password non coincidono.";
            $password_error=true;
        }
        
        if(!$password_error){
            $password=request('password');
            $password=password_hash($password,PASSWORD_BCRYPT);
        }
        
        if(isset($_FILES['image'])){
            if($_FILES['image']['error']===0){
                if($_FILES['image']['size']<2000000){
                    $type = exif_imagetype($_FILES['image']['tmp_name']);
                    $allowedExt = array(IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_GIF => 'gif');
                    if(isset($allowedExt[$type])){
                        $fileName = uniqid('', true).".".$allowedExt[$type];
                        move_uploaded_file($_FILES['image']['tmp_name'], "uploads".DIRECTORY_SEPARATOR."$fileName");
                    }else{
                        $errors[]="Inserisci un'immagine (jpeg, png, jpg, gif).";
                    }
                } else {
                    $errors[]="L'immagine non può avere dimensioni superiori a 2MB.";
                }
                
            } else {
                $fileName="defaultAvatar.jpg";
            }
        }
        //se l'utente è un impiegato verifico anche questi altri campi
        
        if(request('tipo')==="impiegato"){
            if(request('impiego')===null|| request('dataAssunzione')===null|| request('stipendio')===null){
                $errors[]="Compila tutti i campi.";
                return view('signup')
                    ->with('csrf_token', csrf_token())
                    ->with('old_name', request('nome'))
                    ->with('old_surname', request('cognome'))
                    ->with('old_username', request('username'))
                    ->with('old_email', request('email'))
                    ->with('checkCliente', $checkCliente)
                    ->with('checkImpiegato', $checkImpiegato)
                    ->with('impieghi',$impieghi)
                    ->with('old_assunzione', request('dataAssunzione'))
                    ->with('old_stipendio', request('stipendio'))
                    ->with('errors', $errors);
            }
            if(request('dataAssunzione')>date("Y-m-d")){//se la data è maggiore di quella odierna dà errore
                $errors[]="Inserire una data passata.";
            }
            
            if(!ctype_digit(request('stipendio'))){
                $errors[]="Il campo stipendio può contenere solo numeri.";
            }
        }
        if(count($errors)===0){
            $user = new User;
            $user->nome=request('nome');
            $user->cognome=request('cognome');
            $user->username=request('username');
            $user->email=request('email');
            $user->password=$password;
            $user->propic=$fileName;
            if(request('tipo')==="cliente"){
                $user->impiegato=false;
            } else if(request('tipo')==="impiegato"){
                $user->impiegato=true;
                $user->impiego=request('impiego');
                $user->dataAssunzione=request('dataAssunzione')." 00:00:00";
                $user->stipendio=request('stipendio');
            }
            $user->save();
            Session::put('id',$user->id);
            Session::put('tipo',$user->impiegato);
            Session::put('username',$user->username);
            return redirect("home");
        } else {
                return view('signup')
                    ->with('app_url', env('APP_URL'))
                    ->with('app_folder', env('APP_FOLDER'))
                    ->with('csrf_token', csrf_token())
                    ->with('old_name', request('nome'))
                    ->with('old_surname', request('cognome'))
                    ->with('old_username', request('username'))
                    ->with('old_email', request('email'))
                    ->with('checkCliente', $checkCliente)
                    ->with('checkImpiegato', $checkImpiegato)
                    ->with('impieghi', $impieghi)
                    ->with('old_assunzione', request('dataAssunzione'))
                    ->with('old_stipendio', request('stipendio'))
                    ->with('errors', $errors);
        }
    }
    public function checkEmail($email){
        $error=false;
        $user=User::where('email',$email)->first();
        if(isset($user)){
            $error=true;
        }
        return $error;
    }
    public function checkUsername($username){
        $error=false;
        $user=User::where('username',$username)->first();
        if(isset($user)){
            $error=true;
        }
        return $error;
    }
}
