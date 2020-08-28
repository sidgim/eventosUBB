<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoEvento extends Model
{
    protected $primaryKey = 'idtipoEvento';
    protected $table ='tipoEvento';
    protected $fillable = [
        'tipoEvento'
    ];

    public function tipoEvento(){
        return $this->hasMany('App\Evento');
    }
}
