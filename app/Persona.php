<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{

    protected $fillable = ['nombre', 'tipo_documento', 'num_documento', 'direccion', 'telefono', 'email'];


    public function proveedor(){
        return $this->hasOne('App\Proveedor');
    }


    /**
     * vamos indicar que uma persona está relacionada de maneira direta com um usuario
     * uma pessoa é pai de um usuario
     * uma pessoa tem um usuário
     * has one - tem um
     */
    public function user(){
        return $this->hasOne('App\User');
    }



}


