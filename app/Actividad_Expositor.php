<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Actividad_Expositor extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'idexpositor_actividad';
    protected $table ='expositor_actividad';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'evento', 'expositor_idExpositor','actividad_idActividad'
    ];
    
    //Relacion de muchos a uno
    public function actividad(){
        return $this->belongsTo('App\Actividad', 'actividad_idActividad');
    }

    //Relacion de muchos a uno
    public function expositor(){
        return $this->belongsTo('App\Expositor', 'expositor_idExpositor');
    }
}
