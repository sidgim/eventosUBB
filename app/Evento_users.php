<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Evento_users extends Model
{
    
    use SoftDeletes;
    protected $primaryKey = 'idevento_users';
    protected $table ='evento_users';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'archivo', 'evento_idEvento','rol_idRol', 'users_id', 'created_ad', 'update_at'
    ];

    //Relacion de muchos a uno
    public function evento(){
        return $this->belongsTo('App\Evento', 'evento_idEvento');
    }

    //Relacion de muchos a uno
    public function rol(){
        return $this->belongsTo('App\Rol', 'rol_idRol');
    }

    //Relacion de muchos a uno
    public function users(){
        return $this->belongsTo('App\User', 'users_id');
    }

}
