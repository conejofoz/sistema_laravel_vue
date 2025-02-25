<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * A vantagem da função invoque é que quando registramos o controlador nas rutas não precisamos especificar 
     * nenhum método na ruta
     */
    public function __invoke(Request $request)
    {
        $anio=date('Y');

        $ingresos=DB::table('ingresos as i')
        ->select(DB::raw('MONTH(i.fecha_hora) as mes'),DB::raw('YEAR(i.fecha_hora) as anio'), DB::raw('SUM(i.total) as total'))
        ->whereYear('i.fecha_hora', $anio)
        ->groupBy(DB::raw('MONTH(i.fecha_hora)'), DB::raw('YEAR(i.fecha_hora)'))
        ->get();
        
        $ventas=DB::table('ventas as v')
        ->select(DB::raw('MONTH(v.fecha_hora) as mes'),DB::raw('YEAR(v.fecha_hora) as anio'), DB::raw('SUM(v.total) as total'))
        ->whereYear('v.fecha_hora', $anio)
        ->groupBy(DB::raw('MONTH(v.fecha_hora)'), DB::raw('YEAR(v.fecha_hora)'))
        ->get();

        return [
            'ingresos' => $ingresos,
            'ventas' => $ventas,
            'anio' => $anio
        ];
    }
}
