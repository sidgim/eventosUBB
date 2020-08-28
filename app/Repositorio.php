<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 


class Repositorio extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'idRepositorio';
    protected $table = 'repositorio';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'nombreMaterial','created_ad', 'update_at','evento_idEvento'
        
    ];

    public function evento (){
        return $this->belongsTo('App\Evento', 'evento_idEvento');
    }
}
