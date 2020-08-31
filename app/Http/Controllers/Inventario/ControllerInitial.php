<?php

namespace App\Http\Controllers\Inventario;

use Illuminate\Http\Request;
use App\Model\sequences;
use App\Model\bienes_activos;
use App\Model\product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ControllerInitial extends Controller
{
    public function __construct(){
        ini_set('max_execution_time', 6500);
        $this->middleware('auth');
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

    public function setAccountProduction(){

        try {
            DB::beginTransaction();
            
            $data_account = array(
                0 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '23/04/2009', 'BIEN' => '001589EB', 'DESCRIPCION' => '1 ENFRIADOR PURIFICADOR, VERTICAL CON RODOS,  COLOR BEIGE CON GRIS, MARCA AIRTEK MODELO AC-1203, SERIE 22101107-0261, DIGITAL, CON CONTROL REMOTO, SEGÚN FACTURA CAMBIARIA  SERIE MMM, NO.15410, A NOMBRE DE AGENCIAS WAY S.A. INGRESO 4243-09.', 'CANTIDAD' => '1', 'COSTO' => '748', 'ALZA' => '748'),
                1 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '23/04/2009', 'BIEN' => '001589D4', 'DESCRIPCION' => '1 ENFRIADOR PURIFICADOR, VERTICAL CON RODOS,  COLOR BEIGE CON GRIS, MARCA AIRTEK MODELO AC-1203, SERIE 22101107-0145, DIGITAL, CON CONTROL REMOTO, SEGÚN FACTURA CAMBIARIA  SERIE MMM, NO.15410, A NOMBRE DE AGENCIAS WAY S.A. INGRESO 4243-09.', 'CANTIDAD' => '1', 'COSTO' => '748', 'ALZA' => '748'),
                2 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '23/04/2009', 'BIEN' => '001589C6', 'DESCRIPCION' => '1 ENFRIADOR PURIFICADOR, VERTICAL CON RODOS,  COLOR BEIGE CON GRIS, MARCA AIRTEK MODELO AC-1203, SERIE 22101107-0289, DIGITAL, CON CONTROL REMOTO, SEGÚN FACTURA CAMBIARIA  SERIE MMM, NO.15410, A NOMBRE DE AGENCIAS WAY S.A. INGRESO 4243-09.', 'CANTIDAD' => '1', 'COSTO' => '748', 'ALZA' => '748'),
                3 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '23/04/2009', 'BIEN' => '001589EE', 'DESCRIPCION' => '1 ENFRIADOR PURIFICADOR, VERTICAL CON RODOS,  COLOR BEIGE CON GRIS, MARCA AIRTEK MODELO AC-1203, SERIE 22101107-0171, DIGITAL, CON CONTROL REMOTO, SEGÚN FACTURA CAMBIARIA  SERIE MMM, NO.15410, A NOMBRE DE AGENCIAS WAY S.A. INGRESO 4243-09.', 'CANTIDAD' => '1', 'COSTO' => '748', 'ALZA' => '748'),
                4 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '23/04/2009', 'BIEN' => '001589CB', 'DESCRIPCION' => '1 ENFRIADOR PURIFICADOR, VERTICAL CON RODOS,  COLOR BEIGE CON GRIS, MARCA AIRTEK MODELO AC-1203, SERIE 22101107-0056, DIGITAL, CON CONTROL REMOTO, SEGÚN FACTURA CAMBIARIA  SERIE MMM, NO.15410, A NOMBRE DE AGENCIAS WAY S.A. INGRESO 4243-09.', 'CANTIDAD' => '1', 'COSTO' => '748', 'ALZA' => '748'),
                5 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '23/04/2009', 'BIEN' => '001589F2', 'DESCRIPCION' => '1 ENFRIADOR PURIFICADOR, VERTICAL CON RODOS,  COLOR BEIGE CON GRIS, MARCA AIRTEK MODELO AC-1203, SERIE 22101107-0058, DIGITAL, CON CONTROL REMOTO, SEGÚN FACTURA CAMBIARIA  SERIE MMM, NO.15410, A NOMBRE DE AGENCIAS WAY S.A. INGRESO 4243-09.', 'CANTIDAD' => '1', 'COSTO' => '748', 'ALZA' => '748'),
                6 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '23/04/2009', 'BIEN' => '001589DE', 'DESCRIPCION' => '1 ENFRIADOR PURIFICADOR, VERTICAL CON RODOS,  COLOR BEIGE CON GRIS, MARCA AIRTEK MODELO AC-1203, SERIE 22101107-0112, DIGITAL, CON CONTROL REMOTO, SEGÚN FACTURA CAMBIARIA  SERIE MMM, NO.15410, A NOMBRE DE AGENCIAS WAY S.A. INGRESO 4243-09.', 'CANTIDAD' => '1', 'COSTO' => '748', 'ALZA' => '748'),
                7 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '23/04/2009', 'BIEN' => '001589D0', 'DESCRIPCION' => '1 ENFRIADOR PURIFICADOR, VERTICAL CON RODOS,  COLOR BEIGE CON GRIS, MARCA AIRTEK MODELO AC-1203, SERIE 22101107-0247, DIGITAL, CON CONTROL REMOTO, SEGÚN FACTURA CAMBIARIA  SERIE MMM, NO.15410, A NOMBRE DE AGENCIAS WAY S.A. INGRESO 4243-09.', 'CANTIDAD' => '1', 'COSTO' => '748', 'ALZA' => '748'),
                8 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '23/04/2009', 'BIEN' => '001589C3', 'DESCRIPCION' => '1 ENFRIADOR PURIFICADOR, VERTICAL CON RODOS,  COLOR BEIGE CON GRIS, MARCA AIRTEK MODELO AC-1203, SERIE 22101107-0180, DIGITAL, CON CONTROL REMOTO, SEGÚN FACTURA CAMBIARIA  SERIE MMM, NO.15410, A NOMBRE DE AGENCIAS WAY S.A. INGRESO 4243-09.', 'CANTIDAD' => '1', 'COSTO' => '748', 'ALZA' => '748'),
                9 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '23/04/2009', 'BIEN' => '001589D6', 'DESCRIPCION' => '1 ENFRIADOR PURIFICADOR, VERTICAL CON RODOS,  COLOR BEIGE CON GRIS, MARCA AIRTEK MODELO AC-1203, SERIE 22101107-0176, DIGITAL, CON CONTROL REMOTO, SEGÚN FACTURA CAMBIARIA  SERIE MMM, NO.15410, A NOMBRE DE AGENCIAS WAY S.A. INGRESO 4243-09.', 'CANTIDAD' => '1', 'COSTO' => '748', 'ALZA' => '748'),
                10 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '23/04/2009', 'BIEN' => '001589D7', 'DESCRIPCION' => '1 ENFRIADOR PURIFICADOR, VERTICAL CON RODOS,  COLOR BEIGE CON GRIS, MARCA AIRTEK MODELO AC-1203, SERIE 22101107-0254, DIGITAL, CON CONTROL REMOTO, SEGÚN FACTURA CAMBIARIA  SERIE MMM, NO.15410, A NOMBRE DE AGENCIAS WAY S.A. INGRESO 4243-09.', 'CANTIDAD' => '1', 'COSTO' => '748', 'ALZA' => '748'),
                11 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '23/04/2009', 'BIEN' => '001589E7', 'DESCRIPCION' => '1 ENFRIADOR PURIFICADOR, VERTICAL CON RODOS,  COLOR BEIGE CON GRIS, MARCA AIRTEK MODELO AC-1203, SERIE 22101107-0237, DIGITAL, CON CONTROL REMOTO, SEGÚN FACTURA CAMBIARIA  SERIE MMM, NO.15410, A NOMBRE DE AGENCIAS WAY S.A. INGRESO 4243-09.', 'CANTIDAD' => '1', 'COSTO' => '748', 'ALZA' => '748'),
                12 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '01/07/2008', 'BIEN' => '001079E7', 'DESCRIPCION' => '1 BOMBA CENTRIFUGA HORIZONTAL COLOR AZUL, MARCA AEROMOTOR,  MODELO SPCM-300, SERIE 1D08R, CON SU TANQUE HIDRONEUMATICO COLOR GRIS, TAPON DE PLASTICO COLOR NEGRO EN PARTE SUPERIOR  Y BASE DE PLASTICO COLOR NEGRO EN LA PARTE INFERIOR,  MARCA WELL-MATE, SIN MODELO, SERIE 059080501, SEGÚN FACTURA SERIE A NO. 351, A NOMBRE DE MULTISERVICIOS J.R.C., INGRESO 3311-08', 'CANTIDAD' => '1', 'COSTO' => '15400', 'ALZA' => '15400'),
                13 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '14/10/2004', 'BIEN' => '0003076C', 'DESCRIPCION' => 'BOMBA CENTRIFUGA HORIZONTAL AMERICANA, MARCA MYERS QP50B ACOPLADA CON MOTOR GENERAL ELECTRIC DE 5 HP, DE COLOR AZUL , SERIE 265965000, MODELO KP50B, REF. 6-15/32,   SEGÚN FACTURA NO. 6086 DE HDROSUMER, HIDRAULICA "PEÑALONZO", ING. 691-04/906259', 'CANTIDAD' => '1', 'COSTO' => '10531', 'ALZA' => '10531'),
                14 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '04/08/2006', 'BIEN' => '000CDF29', 'DESCRIPCION' => '1 BOMBA SUMERGIBLE PARA AGUA DE POZO MECANICO SEGÚN FACTURA NO. 336', 'CANTIDAD' => '1', 'COSTO' => '18252.25', 'ALZA' => '18252.25'),
                15 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '08/08/2006', 'BIEN' => '000CE30B', 'DESCRIPCION' => '1 EXTRACTOR DE OLORES (VENTILADOR),  FAN A647A, REFERENCIA NO.YJF-20(E219206), DE 120 VOLTIOS 60HZ, 0.60 AMPERIOS, INCLUYE: 1 MOTOR CON REFENCIA NO.3900-2059-000  MOTOR ASSY Y UNA REGILLA COLOR BLANCO, PLASTICA, SEGÙN FACTURA  SERIE: C1 NO.23016 DE INSTALACIONES MODERNAS, S.A.', 'CANTIDAD' => '1', 'COSTO' => '188.15', 'ALZA' => '188.15'),
                16 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '08/08/2006', 'BIEN' => '000CE30C', 'DESCRIPCION' => '1 EXTRACTOR DE OLORES (VENTILADOR),  FAN A647A, REFERENCIA NO.YJF-20(E219206), DE 120 VOLTIOS 60HZ, 0.60 AMPERIOS, INCLUYE: 1 MOTOR CON REFENCIA NO.3900-2059-000  MOTOR ASSY Y UNA REGILLA COLOR BLANCO, PLASTICA, SEGÙN FACTURA  SERIE: C1 NO.23016 DE INSTALACIONES MODERNAS, S.A.', 'CANTIDAD' => '1', 'COSTO' => '188.15', 'ALZA' => '188.15'),
                17 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '15/12/2008', 'BIEN' => '0011E5AB', 'DESCRIPCION' => '1 GUILLOTINA PARA PAPEL, MARCA STUDMARK, MODELO ST-041187, SIN SERIE, BASE DE MADERA DURA, GUIA PARA AJUSTE DEL PAPEL CON TORNILLO DE FIJACION, LAMINA DE CORTE DE ACERO DE CARBON, SEGÚN FACTURA CAMBIARIA LIBRE DE PROTESTO SERIE B NO. 06434, INGRESO 3918-2008', 'CANTIDAD' => '1', 'COSTO' => '709', 'ALZA' => '709'),
                18 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '15/12/2008', 'BIEN' => '0011E593', 'DESCRIPCION' => '1 GUILLOTINA PARA PAPEL, MARCA STUDMARK, MODELO ST-041187, SIN SERIE, BASE DE MADERA DURA, GUIA PARA AJUSTE DEL PAPEL CON TORNILLO DE FIJACION, LAMINA DE CORTE DE ACERO DE CARBON, SEGÚN FACTURA CAMBIARIA LIBRE DE PROTESTO SERIE B NO. 06434, INGRESO 3918-2008', 'CANTIDAD' => '1', 'COSTO' => '709', 'ALZA' => '709'),
                19 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '06/11/2006', 'BIEN' => '000D6254', 'DESCRIPCION' => '1 MOTOR VENTILADOR COLOR GRIS MARCA SMART MOTORS,  MODELO CE 3729, SERIE NO.5KCP-39F,  PARA UNA MANEJADORA DE AIRE ACONDICIONADO SEGÙN FACTURA NO.00557 A NOMBRE DE DISTRIBUIDORA SIERRA IMPORTADORA Y EXPORTADORA INGRESO NO. 1882-06', 'CANTIDAD' => '1', 'COSTO' => '3900', 'ALZA' => '3900'),
                20 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '08/08/2006', 'BIEN' => '000CE352', 'DESCRIPCION' => '1 MOTOR VENTILADOR,  PARA UNA MANEJADORA DE AIRE ACONDICIONADO, SEGÙN FACTURA NO.00528 DE DISTRIBUIDORA SIERRA IMPORTADORA Y EXPORTADORA.', 'CANTIDAD' => '1', 'COSTO' => '2464', 'ALZA' => '2464'),
                21 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '01/07/2008', 'BIEN' => '0010853A', 'DESCRIPCION' => '1 MOTOR COLOR NEGRO,  PARA VENTILADOR DE MANEJADORA DE AIRE ACONDICIONADO, MARCA EMERSON, MODELO S48M40A01, SERIE S48M40A01, DE 1/4 DE CABALLO 220V, SEGÚN FACTURA NO.605, A NOMBRE DE DISTRIBUIDORA SIERRA IMPORTADORA Y EXPORTADORA, INGRESO 3319-08.', 'CANTIDAD' => '1', 'COSTO' => '2600', 'ALZA' => '2600'),
                22 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '05/10/2006', 'BIEN' => '000D2C2E', 'DESCRIPCION' => '1 COMPRESOR DE AIRE ACONDICIONADO DE DOS TONELADAS,  COLOR NEGRO, SEGÚN FACTURA NO. 556, A NOMBRE DE DISTRIBUIDORA SIERRA IMPORTADORA Y EXPORTADORA, INGRESO 1776-06', 'CANTIDAD' => '1', 'COSTO' => '5148', 'ALZA' => '5148'),
                23 => array('UE' => '1', 'CUENTA' => '1', 'FECHA' => '21/09/2006', 'BIEN' => '000D109D', 'DESCRIPCION' => '1 EQUIPO DE AIRE ACONDICINADO, COMPUESTO POR UN CONDENSADOR, TIPO SPLIT, DE 5 TONELADAS,  COLOR GRIS, SIN MODELO , SERIE NO. JSA060200570, CON MOTOR INTERNO CON ASPAS  MARCA COPELAND, MANEJADORA TIPO SPLIT, DE 5 TONELADAS CON CAPACIDAD DE 2,000 CFM (PIES CUBICO POR MINUTO), MODELO NO. 903546B, SERIE NO. AHDO51103253, SEGÚN FACTURA NO. 553, A NOMBRE DE DISTRUBUIDORA SIERRA IMPORTADORA Y EXPORTADORA, INGRESO NO. 1711-06', 'CANTIDAD' => '1', 'COSTO' => '22000', 'ALZA' => '22000'),
            );
            
            foreach ($data_account as $key => $value) { 

                $secuencia = $this->sequences_data("activos");
                $secuencia = json_decode(json_encode($secuencia));

                $producto = $this->addProductBienes($value['DESCRIPCION']);
                $producto = json_decode(json_encode($producto));


                $activos_bien = new bienes_activos;

                $activos_bien->id_activo = $secuencia->original->value;
                $activos_bien->fecha_fiscal = 2020;
                $activos_bien->id_entidad = 1;
                $activos_bien->id_unidad = $value['UE'];

                $activos_bien->id_grupo = 3;
                $activos_bien->id_categoria = 9;
                $activos_bien->id_seccion = 24;
                $activos_bien->id_tipo = 124;
                $activos_bien->id_bien = 1;
                $activos_bien->id_estado = 1;

                $activos_bien->id_producto = $producto->original->id_producto;
                
                $activos_bien->comentario = "";
                $activos_bien->modelo = "";
                $activos_bien->serie = "";
                $activos_bien->marca = "";

                $activos_bien->fecha_ingreso = $value['FECHA'];

                $activos_bien->lugar_fisico = "";
                $activos_bien->id_empleado = 1;

                $activos_bien->id_dependencia = 1;
                $activos_bien->id_cuenta = $value['CUENTA'];
                $activos_bien->id_documento_respaldo = 1;
                $activos_bien->id_secuencia = 1;
                $activos_bien->no_factura = "";

                $activos_bien->valor_costo = $value['COSTO'];

                $activos_bien->serie_factura = "A";
                $activos_bien->nit_proveedor = "sin nit";
                $activos_bien->alza = $value['ALZA'];

                $activos_bien->baja = "0";

                $activos_bien->codigo_sicoin = $value['BIEN'];
                $activos_bien->cantidad = $value['CANTIDAD'];
                $activos_bien->save();

            }

            DB::commit();
            return response()->json(true,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);
        }


    }
    public function setDataExcel(Request $request){ 

        try {
            DB::beginTransaction();
            
            
            $data_account = $request->data_excel;
            
            foreach ($data_account as $key => $value) { 

                
                $secuencia = $this->sequences_data("activos");
                $secuencia = json_decode(json_encode($secuencia));

                $producto = $this->addProductBienes($value['descripcion']);
                $producto = json_decode(json_encode($producto));


                $activos_bien = new bienes_activos;

                $activos_bien->id_activo = $secuencia->original->value;
                $activos_bien->fecha_fiscal = 2020;
                $activos_bien->id_entidad = 1;
                $activos_bien->id_unidad = $request->UE;

                $activos_bien->id_grupo = 3;
                $activos_bien->id_categoria = 9;
                $activos_bien->id_seccion = 24;
                $activos_bien->id_tipo = 124;
                $activos_bien->id_bien = 1;
                $activos_bien->id_estado = 1;

                $activos_bien->id_producto = $producto->original->id_producto;
                
                $activos_bien->comentario = "";
                $activos_bien->modelo = "";
                $activos_bien->serie = "";
                $activos_bien->marca = "";

                $activos_bien->fecha_ingreso = $value['fecha'];

                $activos_bien->lugar_fisico = "";
                $activos_bien->id_empleado = 1;

                $activos_bien->id_dependencia = 1;
                $activos_bien->id_cuenta = $request->CUENTA;
                $activos_bien->id_documento_respaldo = 1;
                $activos_bien->id_secuencia = 1;
                $activos_bien->no_factura = "";

                $activos_bien->valor_costo = $value['costo'];

                $activos_bien->serie_factura = "A";
                $activos_bien->nit_proveedor = "sin nit";
                $activos_bien->alza = $value['alza'];

                $activos_bien->baja = "0";

                $activos_bien->codigo_sicoin = $value['bien'];
                $activos_bien->cantidad = $value['cantidad'];
                $activos_bien->save();

            }

            DB::commit();
            return response()->json(true,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);
        }


    }

}
