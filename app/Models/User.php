<?php

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $hidden = ['password'];
    public $timestamps=false;

    public function impiego()
    {
        return $this->belongsTo('Azienda');
    }

    public function impieghiPassati()
    {
        return $this->belongsToMany('Azienda','impiegoPassato','user','impiegoPassato');
    }
    public function recensioni()
    {
        return $this->belongsToMany('Prodotto','recensioni','user','prodotto');
    }
    public function like()
    {
        return $this->belongsToMany('Recensione','like_recensioni','user','recensione');
    }
    public function utente_prodotto()
    {
        return $this->belongsToMany('Prodotto','utente_prodotto','user','prodotto');
    }
}

?>