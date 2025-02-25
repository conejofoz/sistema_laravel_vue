<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //para trabalhar com transações
use Carbon\Carbon; //para trabalhar com datas
use App\Venta;
use App\DetalleVenta;
use App\Articulo; // eu
use PhpParser\Node\Stmt\TryCatch;
use Mockery\Exception;
use App\User;
use App\Notifications\NotifyAdmin;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            //$ventas = Persona::paginate(5);
            $ventas = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
                ->join('users', 'ventas.idusuario', '=', 'users.id')
                ->select(
                    'ventas.id',
                    'ventas.tipo_comprobante',
                    'ventas.serie_comprobante',
                    'ventas.num_comprobante',
                    'ventas.fecha_hora',
                    'ventas.impuesto',
                    'ventas.total',
                    'ventas.estado',
                    'personas.nombre',
                    'users.usuario'
                )
                ->orderBy('ventas.id', 'desc')->paginate(15);
        } else {
            $ventas = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
                ->join('users', 'ventas.idusuario', '=', 'users.id')
                ->select(
                    'ventas.id',
                    'ventas.tipo_comprobante',
                    'ventas.serie_comprobante',
                    'ventas.num_comprobante',
                    'ventas.fecha_hora',
                    'ventas.impuesto',
                    'ventas.total',
                    'ventas.estado',
                    'personas.nombre',
                    'users.usuario'
                )
                ->where('ventas.' . $criterio, 'like', '%' . $buscar . '%')->orderBy('ventas.id', 'desc')->paginate(15);
        }
        return [
            'pagination' => [
                'total'         => $ventas->total(),
                'current_page'  => $ventas->currentPage(),
                'per_page'      => $ventas->perPage(),
                'last_page'     => $ventas->lastPage(),
                'from'          => $ventas->firstItem(),
                'to'            => $ventas->lastItem(),
            ],
            'ventas' => $ventas
        ];
    }




    public function obtenerCabecera(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $venta = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
            ->join('users', 'ventas.idusuario', '=', 'users.id')
            ->select(
                'ventas.id',
                'ventas.tipo_comprobante',
                'ventas.serie_comprobante',
                'ventas.num_comprobante',
                'ventas.fecha_hora',
                'ventas.impuesto',
                'ventas.total',
                'ventas.estado',
                'personas.nombre',
                'users.usuario'
            )
            ->where('ventas.id', '=', $id)
            ->orderBy('ventas.id', 'desc')->take(1)->get();
        return [
            'venta' => $venta
        ];
    }




    public function obtenerDetalles(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
            ->select(
                'detalle_ventas.cantidad',
                'detalle_ventas.precio',
                'detalle_ventas.descuento',
                'articulos.nombre as articulo'
            )
            ->where('detalle_ventas.idventa', '=', $id)
            ->orderBy('detalle_ventas.id', 'desc')->get();
        return [
            'detalles' => $detalles
        ];
    }



    public function pdf(Request $request, $id)
    {
        $venta = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
            ->join('users', 'ventas.idusuario', '=', 'users.id')
            ->select(
                'ventas.id',
                'ventas.tipo_comprobante',
                'ventas.serie_comprobante',
                'ventas.num_comprobante',
                'ventas.created_at',
                'ventas.impuesto',
                'ventas.total',
                'ventas.estado',
                'personas.nombre',
                'personas.tipo_documento',
                'personas.num_documento',
                'personas.direccion',
                'personas.email',
                'personas.telefono',
                'users.usuario'
            )
            ->where('ventas.id', '=', $id)
            ->orderBy('ventas.id', 'desc')->take(1)->get();

        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
            ->select('detalle_ventas.cantidad', 'detalle_ventas.precio', 'detalle_ventas.descuento', 'articulos.nombre as articulo')
            ->where('detalle_ventas.idventa', '=', $id)
            ->orderBy('detalle_ventas.id', 'desc')->get();

        $numventa = Venta::select('num_comprobante')->where('id', $id)->get();

        $pdf = \PDF::loadView('pdf.venta', ['venta' => $venta, 'detalles' => $detalles]);
        //return $pdf->download('venta-'.$numventa[0]->num_comprobante.'.pdf');
        return $pdf->stream('venta-' . $numventa[0]->num_comprobante . '.pdf');
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (!$request->ajax()) return redirect('/');

        try {

            DB::beginTransaction();

            $mytime = Carbon::now('America/Sao_Paulo');

            $venta = new Venta();
            $venta->idcliente = $request->idcliente;
            $venta->idusuario = \Auth::user()->id;
            $venta->tipo_comprobante = $request->tipo_comprobante;
            $venta->serie_comprobante = $request->serie_comprobante;
            $venta->num_comprobante = $request->num_comprobante;
            $venta->fecha_hora = $mytime->toDateString();
            $venta->impuesto = $request->impuesto;
            $venta->total = $request->total;
            $venta->estado = 'Registrado';
            $venta->save();

            $detalles = $request->data;

            foreach ($detalles as $ep => $det) {
                $detalle = new DetalleVenta();
                $detalle->idventa = $venta->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->descuento = $det['descuento'];
                $detalle->save();
            }

            foreach ($detalles as $et => $item) {
                $articulo = Articulo::findOrFail($item['idarticulo']);
                $articulo->stock = $articulo->stock - $item['cantidad'];
                $articulo->save();
            }

            $fechaActual = date('Y-m-d');
            $numVentas = DB::table('ventas')->whereDate('created_at', $fechaActual)->count();
            $numIngresos = DB::table('ingresos')->whereDate('created_at', $fechaActual)->count();
            $arregloDatos = [
                'ventas' => [
                    'numero' => $numVentas,
                    'msj' => 'Ventas'
                ],
                'ingresos' => [
                    'numero' => $numIngresos,
                    'msj' => 'Ingresos'
                ]
            ];
            $allUsers = User::all();

            foreach ($allUsers as $notificar) {
                User::findOrFail($notificar->id)->notify(new NotifyAdmin($arregloDatos));
            }


            DB::commit();

            /**
             * usado na impressão
             */
            return [
                'id' => $venta->id
            ];
        } catch (Exception $e) {
            DB::rollBack();
        }
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */



    public function desactivarVenta(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;

            if (!$request->ajax()) return redirect('/');
            $venta = Venta::findOrFail($request->id);
            $venta->estado = 'Anulado';
            $venta->save();

            $detalles = DetalleVenta::join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
                ->select(
                    'detalle_ventas.cantidad',
                    'detalle_ventas.precio',
                    'detalle_ventas.descuento',
                    'articulos.nombre as articulo',
                    'detalle_ventas.idarticulo'
                )
                ->where('detalle_ventas.idventa', '=', $id)
                ->orderBy('detalle_ventas.id', 'desc')->get();
            foreach ($detalles as $key => $item) {
                $articulo = Articulo::findOrFail($item['idarticulo']);
                $articulo->stock = $articulo->stock + $item['cantidad'];
                $articulo->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
