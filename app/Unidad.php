<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $primaryKey = 'idUnidad';
    protected $table ='unidad';
    protected $fillable = [
        'nombreUnidad', 'logoUnidad', 'sede'
    ];

    public function users_unidad(){
        return $this->hasMany('App\users_unidad');
    }
}
