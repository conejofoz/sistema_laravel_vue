<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    /**Esta propriedade indica os campos que vão enviar e receber valores
     * no caso desta tabela o único campo que não vamos usar é o id
     */
    protected $fillable = [
        'idcliente',
        'idusuario',
        'tipo_comprobante',
        'serie_comprobante',
        'num_comprobante',
        'fecha_hora',
        'impuesto',
        'total',
        'estado'
    ];

    
}
