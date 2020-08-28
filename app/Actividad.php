<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    // use SoftDeletes;
    protected $primaryKey = 'idActividad';
    protected $table = 'actividad';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'nombreActividad', 'horaInicioActividad', 'horaFinActividad', 'ubicacionActividad', 'descripcionActividad',
        'jornada_idJornada', 'actividadParalela', 'cupos',
    ];

    public function jornada()
    {
        return $this->belongsTo('App\Jornada', 'jornada_idJornada');
    }

    public function actividad_expositor()
    {
        return $this->hasMany('App\Actividad_Expositor');
    }

    //Convierte antes de almacenar en minuscula todas las palabras
    public function setDescripcionActividad($valor)
    {
        $this->attributes['descripcionActividad'] = strtolower($valor);
    }

    //obtiene de la base de datos en mayuscula la primera letra
    public function getDescripcionActividad($valor)
    {
        return ucfirst($valor);
    }
}
