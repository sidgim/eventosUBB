<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $primaryKey = 'idCiudad';
    public $timestamps = false;
    protected $table ='ciudad';
    protected $fillable = [
        'nombreCiudad'
    ];

    public function evento(){
        return $this->hasMany('App\Evento');
    }
}
