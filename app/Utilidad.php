<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Utilidad extends Model
{
    protected $primaryKey = 'idUtilidad';
    public $timestamps = false;
    protected $table ='utilidad';
    protected $fillable = [
        'coordenadax, coordenaday , tipoFuente, tamanioFuente, colorFuente, imagen'
    ];

    public function evento(){
        return $this->hasMany('App\Evento');
    }
}
