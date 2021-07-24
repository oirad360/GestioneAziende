<?php

use Illuminate\Database\Eloquent\Model;

class Suggerimento extends Model
{
    public $timestamps=false;
    protected $table="suggerimenti";
    public function prodotto()
    {
        return $this->belongsTo('Prodotto');
    }

}

?>