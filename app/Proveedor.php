<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    /*neste caso tem que especificar o nome da tabela por causa do plural
    se deixasse ele gerar seria proveedors*/
    protected $table = 'proveedores';
    protected $fillable = [
        'id',
        'contacto',
        'telefono_contacto'
    ];

    /**
     * essa tabela não tem os campos timestamp nesse caso tem que declarar como false
     */
    public $timestamps = false;

    public function persona(){
        return $this->belongsTo('App\Persona');
    }
}
