<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'email', 
        'password',
        'condicion',
        'idrol'
    ];

    /**
     * The attributes that should be hidden for arrays.
     * Este código vai impedir que a senha seja mostrada nas consultas
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * declarar a variavel $timestamps como false porque a tabela users não tem os campos create_at e update_at
     */

     public $timestamps = false;


    /**
     * Vamos indicar que um usuário pertence a um Rol
     * belongs to - pertence a
     * um usuario pertence a uma pessoa
     */
    public function rol(){
        return $this->belongsTo('App\Rol');
    }

    /**
     * Vamos indicar que um usuário faz referência a um pessoa
     */
    public function persona(){
        return $this->belongsTo('App\Persona');
    }

    

}
