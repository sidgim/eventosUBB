<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $primaryKey = 'idCategoria';
    public $timestamps = false;
    protected $table ='categoria';
    protected $fillable = [
        'nombreCategoria'
    ];

    public function evento(){
        return $this->hasMany('App\Evento');
    }
}

