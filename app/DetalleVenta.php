<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas'; //o laravel iria fazer detalleventas
    protected $fillable = [
        'idventa',
        'idarticulo',
        'cantidad',
        'precio',
        'descuento'
    ];

    public $timestamps = false;
}
