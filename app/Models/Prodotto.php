<?php

use Illuminate\Database\Eloquent\Model;

class Prodotto extends Model
{
    public $timestamps=false;
    protected $table="prodotto";
    public function azienda()
    {
        return $this->belongsTo('Azienda');
    }

    public function suggerimenti()
    {
        return $this->hasMany('Suggerimento','id_prodotto');
    }

    public function recensioni()
    {
        return $this->belongsToMany('User','recensioni','prodotto','user');
    }

    public function utente_prodotto()
    {
        return $this->belongsToMany('User','utente_prodotto','prodotto','user');
    }
}

?>