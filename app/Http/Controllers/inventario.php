<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\bienes_activos;
use App\Model\sequences;
use App\Model\product;

use App\Model\cuentas_activo;
use App\Model\checkInventory;
use Illuminate\Support\Facades\DB;

class inventario extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('admin.home');
    }

    public function showProduct(){
        return view('product.index');
    }

    public function showActive(){
        return view('active.index');
    }

    public function showSearch(){
        return view('Scanner.scanner');
    }

    public function showList(){
        return view('active.Barcode');
    }
    public function showReport(){
        return view('active.reportInventory');
    }

    public function showInventory(){
        return view('Scanner.scannerSearch');
    }

    public function sequences_data($tabla){
        
        if($data = sequences::where('name','=',$tabla)->select('value')->count() === 0){
            $value = 0;
            $vacio = true;
            
        }else{
            $value = sequences::select('value')->where('name','=', $tabla)->get(); 
            $vacio = false;
            
        };

        if($vacio === true){
            $data = new sequences;
            $data->name = $tabla;
            $data->value = $value + 1;
            $data->save();
        }else{
            $cantidad = $data = sequences::where('name','=',$tabla)->select('value')->count();
            $data = new sequences;
            $data->name = $tabla;
            $data->value = $cantidad+1;
            $data->save();
        }
        return response()->json($data,200);
    }

    public function getYear(){
        // $date = Carbon::now('America/Guatemala');
        $date = date('Y');
        return response()->json($date,200);
    }

    public function addProductBienes($new_producto){

        try{
            $secuencia = $this->sequences_data("productos");
            $secuencia = json_decode(json_encode($secuencia)) ;
    
            $product = new product;
            $product->id_producto = $secuencia->original->value;
            $product->descripcion = $new_producto;
            $product->estatus = 'A';
            $product->save();

            DB::commit();
            return response()->json($product,200);
        }
        catch(\Exceptio $e){
            DB::rollBack();
            return response()->json(false,500);
        }

    }

    public function addActivosBienes(Request $request){
        try {
            DB::beginTransaction();

            $secuencia = $this->sequences_data("activos");
            $secuencia = json_decode(json_encode($secuencia));

            $producto = $this->addProductBienes($request->producto);
            $producto = json_decode(json_encode($producto));

            $fechaIngreso = date('Y/m/d', strtotime($request->fIngreso));

            $activos_bien = new bienes_activos;

            $activos_bien->id_activo = $secuencia->original->value;
            $activos_bien->fecha_fiscal = $request->nowYear;
            $activos_bien->id_entidad = $request->entity;
            $activos_bien->id_unidad = $request->unidad;
            $activos_bien->id_grupo = $request->grupo;
            $activos_bien->id_categoria = $request->categoria;
            $activos_bien->id_seccion = $request->seccion;
            $activos_bien->id_tipo = $request->tipo;
            $activos_bien->id_bien = $request->bien;
            $activos_bien->id_estado = $request->estado;
            $activos_bien->id_producto = $producto->original->id_producto;
            $activos_bien->comentario = $request->comentario;
            $activos_bien->modelo = $request->modelo;
            $activos_bien->serie = $request->serie;
            $activos_bien->marca = $request->marca;
            $activos_bien->fecha_ingreso = $fechaIngreso;
            $activos_bien->lugar_fisico = $request->localidad;
            $activos_bien->id_empleado = $request->empleado;
            $activos_bien->id_dependencia = $request->dependencia;
            $activos_bien->id_cuenta = $request->cuenta;
            $activos_bien->id_documento_respaldo = $request->respaldo;
            $activos_bien->id_secuencia = $request->secuencia;
            $activos_bien->no_factura = $request->nFactura;
            $activos_bien->valor_costo = $request->costo;
            $activos_bien->serie_factura = $request->serieFac;
            $activos_bien->nit_proveedor = $request->proveedor;
            $activos_bien->alza = $request->alza;
            $activos_bien->baja = $request->baja;
            $activos_bien->codigo_sicoin = $request->sicoin;
            $activos_bien->cantidad = $request->cantidad;
            $activos_bien->save();

            DB::commit();

            return response()->json($activos_bien,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th,500);
        }
    }


    public function getAccountInitial(){
        $account = cuentas_activo::all();
        return response()->json($account,200);
    }

    public function setCountInventory(Request $request){
        
        try {
            DB::beginTransaction();
            $secuencia = $this->sequences_data("check_inventories");
            $secuencia = json_decode(json_encode($secuencia)) ;
            
            $inventory = new checkInventory;

            $inventory->id_inventory = $secuencia->original->value;
            $inventory->id_bien = $request->id_activo;
            $inventory->fisico = $request->fisico;
            $inventory->lugar = $request->lugar;
            $inventory->nit_empleado = $request->empleado;
            $inventory->save();
            
            DB::commit();
            return response()->json($inventory,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th,500);
        }

        
    }


}
