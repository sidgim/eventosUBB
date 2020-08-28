<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const USUARIO_VERIFICADO = '1';
    const USUARIO_NO_VERIFICADO = '0';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = 'id';
    protected $table = "users";
    protected $fillable = [
        'nombreUsuario', 'apellidoUsuario','email', 'password',  'avatar','remember_token','google_id' , 'nick','verified'
    ];

     //Relacion de uno a muchos
     public function material(){
        return $this->hasMany('App\Evento_users');
    }

    public function users_unidad(){
        return $this->hasMany('App\users_unidad');
    }

      //Relacion de muchos a uno
      public function perfil(){
        return $this->belongsTo('App\Perfil', 'perfil_idPerfil');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
