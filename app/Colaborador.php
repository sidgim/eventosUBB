<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Colaborador extends Model
{
    protected $primaryKey = 'idColaborador';
    protected $table ='colaborador';
    public $timestamps = false;
    protected $fillable = [
        'nombreColaborador', 'nombreRepresentante','telefonoColaborador', 'correoColaborador', 'sitioWeb','logo', 'evento_idEvento' 
    ];
    public function evento(){
        return $this->belongsTo('App\Evento', 'evento_idEvento');
    }
    public function tipoColaborador(){
        return $this->belongsTo('App\TipoColaborador', 'tipoColaborador_idtipoColaborador');
    }
}
