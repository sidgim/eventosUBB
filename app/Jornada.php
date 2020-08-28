<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jornada extends Model
{

    protected $primaryKey = 'idJornada';
    protected $table = 'jornada';
    public $timestamps = false;
    protected $fillable = [
        'nombreJornada', 'fechaJornada','horaInicioJornada', 'horaFinJornada', 'ubicacionJornada',
        'descripcionJornada', 'evento_idEvento' 
    ];

    public function actividad (){
        return $this->hasMany('App\Actividad');
    }

    public function evento (){
        return $this->belongsTo('App\Evento', 'evento_idEvento');
    }
    
}
