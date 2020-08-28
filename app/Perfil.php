<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $primaryKey = 'idPerfil';
    protected $table ='perfil';
    protected $fillable = [
        'nombrePerfil'
    ];

      //Relacion de uno a muchos
      public function evento(){
        return $this->hasMany('App\Evento');
    }
}
