<?php

use Illuminate\Database\Eloquent\Model;

class Azienda extends Model
{
    public $timestamps=false;
    protected $table="azienda";
    public function impiegati()
    {
        return $this->hasMany('User','impiego');
    }

    public function prodotti()
    {
        return $this->hasMany('Prodotto','produttore');
    }

    public function impiegatiPassati()
    {
        return $this->belongsToMany('Azienda','impiegoPassato','impiegoPassato','user');
    }
}

?>