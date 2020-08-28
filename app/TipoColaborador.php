<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class TipoColaborador extends Model
{
    protected $primaryKey = 'idtipoColaborador';
    protected $table ='tipoColaborador';
    protected $fillable = [
        'tipoColaborador'
    ];

    public function colaborador(){
        return $this->hasMany('App\Colaborador');
    }
}
