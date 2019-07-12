<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Rol extends Model
{
    protected $table = 'roles';
    protected $fillable = ['nombre', 'descripcion', 'condicion'];
    public $timestamps = false;


    /**
     * Vamos indicar que um rol pode ter vários usuarios
     * has many - tem muitos
     */
    public function user(){
        return $this->hasMany('App\User');
    }

}
