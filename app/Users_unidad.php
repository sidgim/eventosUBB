<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Users_unidad extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'idusers_unidad';
    protected $table ='users_unidad';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'perfilnidad', 'unidad_idUnidad','users_id'
    ];

    //Relacion de muchos a uno
    public function user(){
        return $this->belongsTo('App\User', 'users_id');
    }

    //Relacion de muchos a uno
    public function unidad(){
        return $this->belongsTo('App\Unidad', 'unidad_idUnidad');
    }
}
