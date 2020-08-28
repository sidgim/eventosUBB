<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expositor extends Model
{
    protected $primaryKey = 'idExpositor';
    protected $table = 'expositor';
    public $timestamps = false;
    protected $fillable = [
        'nombreExpositor', 'apellidoExpositor', 'sexo', 'correoExpositor', 'empresa',
        'foto', 'telefonoExpositor', 'apellido2Expositor'
    ];
    public function actividad_expositor()
    {
        return $this->hasMany('App\Actividad_Expositor');
    }
}
