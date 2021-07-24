<?php

use Illuminate\Database\Eloquent\Model;

class Recensione extends Model
{
    public $timestamps=false;
    protected $table="recensioni";

    public function like()
    {
        return $this->belongsToMany('User','like_recensioni','recensione','user');
    }
}

?>