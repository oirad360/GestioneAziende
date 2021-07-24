<?php

use Illuminate\Routing\Controller as BaseController;

class CarrelloController extends BaseController{

    public function carrello(){
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
        return view('carrello')
            ->with('app_url', env('APP_URL'))
            ->with('app_folder', env('APP_FOLDER'))
            ->with('csrf_token', csrf_token())
            ->with('username',$user->username)
            ->with('categorieSide',$categorieSide)
            ->with('propicURL',$propicURL)
            ->with('countCarrello',$user->numCarrello);
    }

    public function fetchCarrello(){
        //select titolo, immagine, prezzo, carrello as quantita from utente_prodotto up 
        //join prodotto p on up.prodotto=p.id where carrello!=0 and user=".$_SESSION['id']
        $carrello=UtenteProdotto::where('user',session('id'))->where('carrello','!=',0)->select('prodotto','carrello as quantita')->get();
        foreach($carrello as $prodotto){
            $prodotto['titolo']=Prodotto::find($prodotto->prodotto)->titolo;
            $prodotto['immagine']=Prodotto::find($prodotto->prodotto)->immagine;
            $prodotto['prezzo']=Prodotto::find($prodotto->prodotto)->prezzo;
        }
        return $carrello;
    }

    public function aggiungiCarrello($titolo){
        //select 1 from utente_prodotto where user=".$_SESSION['id']." and prodotto=(select id from prodotto where titolo='$titolo')";
        $id=Prodotto::where('titolo',$titolo)->first()->id;
        $row=UtenteProdotto::where('user',session('id'))->where('prodotto', $id)->first();
        if(isset($row)){
            //update utente_prodotto set carrello=carrello+1 
            //where user=".$_SESSION['id']." and prodotto=(select id from prodotto where titolo='$titolo')
            $row=UtenteProdotto::where('user',session('id'))->where('prodotto', $id)->first();
            $row->carrello=$row->carrello + 1;
            $row->save();
        } else {
            //insert into utente_prodotto(user,prodotto,carrello) values
            //(".$_SESSION['id'].",(select id from prodotto where titolo='$titolo'),1)
            $row=new UtenteProdotto;
            $row->user=session('id');
            $row->prodotto=Prodotto::where('titolo',$titolo)->first()->id;
            $row->carrello=1;
            $row->save();
        }
        //select sum(carrello) from utente_prodotto where user='".$_SESSION['id']."'"
        $countCarrello=UtenteProdotto::where('user',session('id'))->sum('carrello');
        return $countCarrello;
    }

    public function rimuoviCarrello($titolo){
        $id=Prodotto::where('titolo',$titolo)->first()->id;
        //update utente_prodotto set carrello=carrello-1 
        //where user=".$_SESSION['id']." and prodotto=(select id from prodotto where titolo='$titolo')"
        $row=UtenteProdotto::where('user',session('id'))->where('prodotto', $id)->first();
        $row->carrello=$row->carrello - 1;
        $row->save();

        //delete from utente_prodotto where user=".$_SESSION['id']." 
        //and prodotto=(select id from prodotto where titolo='$titolo') and wishlist=false and carrello=0 and acquisto=0
        $rows=UtenteProdotto::where('user',session('id'))->where('prodotto', $id)->where('wishlist',false)->where('carrello',0)->where('acquisto',0)->get();
        foreach($rows as $row){
            $row->delete();
        }
    }
    public function ordina(){
        //update utente_prodotto set acquisto=acquisto + carrello, carrello=0 where carrello!=0 and user=".$_SESSION['id']
        $rows=UtenteProdotto::where('carrello','!=',0)->where('user',session('id'))->get();
        foreach($rows as $row){
            $row->acquisto+=$row->carrello;
            $row->carrello=0;
            $row->save();
        }
    }
}
