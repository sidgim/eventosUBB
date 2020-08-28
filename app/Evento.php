<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Evento extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'idEvento';
    protected $table ='evento';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'nombreEvento', 'ubicacion','direccion', 'detalles', 'imagen','capacidad', 'visibilidad'
    ];

    //Relacion de muchos a uno
    public function ciudad(){
        return $this->belongsTo('App\Ciudad', 'ciudad_idCiudad');
    }

 //Relacion de muchos a uno
 public function utilidad(){
    return $this->belongsTo('App\Utilidad', 'utilidad_idUtilidad');
}

   //Relacion de muchos a uno
   public function tipoEvento(){
    return $this->belongsTo('App\TipoEvento', 'tipoEvento_idtipoEvento');
    }
    
    //Relacion de muchos a uno
    public function categoria(){
        return $this->belongsTo('App\Categoria', 'categoria_idCategoria');
    }

     //Relacion de uno a muchos
     public function material(){
        return $this->hasMany('App\Material');
    }

    public function repositorio(){
        return $this->hasMany('App\Repositorio');
    }

    public function colaborador(){
        return $this->hasMany('App\Colaborador');
    }

    public function evento_users(){
        return $this->hasMany('App\Evento_users');
    }

    public function inscripcion(){
        return $this->hasMany('App\Inscripcion');
    }

    public function jornada(){
        return $this->hasMany('App\Jornada');
    }
}
