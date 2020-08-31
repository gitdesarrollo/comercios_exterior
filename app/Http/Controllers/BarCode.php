<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Milon\Barcode\DNS1D;
use App\Model\bienes_activos;
use App\Model\checkInventory;
use Illuminate\Support\Facades\DB;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Illuminate\Support\Facades\Auth;



class BarCode extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function barTest(){
        
            $barcode = new BarcodeGenerator();
            $barcode->setText("0123456789");
            $barcode->setType(BarcodeGenerator::Code128);
            $barcode->setScale(2);
            $barcode->setThickness(25);
            $barcode->setFontSize(10);
            $code = $barcode->generate();

            $img =  '<img src="data:image/png;base64,'.$code.'" />';

        return $code;
    }

    public function barcodeGet(Request $request){
        $code = '<div class="BarCode">'. DNS1D::getBarcodeHTML($request->codeBar,"C128",2,80,'black',false).'</div>';
        return $code;
    }

    public function BarCodeAll($account){
       
      

        $id_unidad = Auth::user()->id_unidad;

        // dd($id_unidad->id_unidad);

        $activos = bienes_activos::select('productos.descripcion','activos.codigo_sicoin','activos.fecha_ingreso', 'activos.cantidad')
                    ->join('productos','productos.id_producto','=','activos.id_producto')
                    ->where('activos.id_cuenta','=',$account)
                    ->where('activos.id_unidad','=',$id_unidad)->get();

        return $activos;
        
        // return view('active.Barcode',[
        //     "activos" => $activos
        // ]);
    }
    public function BarCodeAllReport(Request $request){
       
        
        // $report = checkInventory::selectRaw('activos.id_activo as code','productos.descripcion as producto','activos.codigo_sicoin as sicoin','activos.fecha_ingreso as fecha','activos.cantidad as cantidad','check_inventories.fisico','(activos.cantidad - check_inventories.fisico) as diferencia','check_inventories.lugar','check_inventories.empleado')
        $report = checkInventory::selectRaw('activos.id_activo,productos.descripcion as producto,activos.codigo_sicoin as sicoin, activos.fecha_ingreso as fecha, activos.cantidad as cantidad,
        check_inventories.fisico,check_inventories.lugar,check_inventories.nit_empleado,
        (activos.cantidad - check_inventories.fisico) AS diferencia')
                    ->join('activos','activos.id_activo','=','check_inventories.id_bien')
                    ->join('productos','productos.id_producto','=','activos.id_producto')
                    ->where('activos.id_cuenta','=',$request->account)->get(); 
    
        return $report; 
        
        // return view('active.Barcode',[
        //     "activos" => $activos
        // ]);
    }

    public function BarCodePrinter($data_account){
      
        $activos = bienes_activos::select('productos.descripcion','activos.codigo_sicoin','activos.fecha_ingreso', 'activos.cantidad')
                    ->join('productos','productos.id_producto','=','activos.id_producto')
                    ->where('id_cuenta','=',$data_account)->get();
        $id = 1;

                    $pdf = \PDF::loadView('active.PrinterBarcode',[
                        "activos" => $activos,
                        "id" => $id
                    ]);
                    $pdf->setPaper('letter', 'portrait');
                    // $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
                    return $pdf->stream("Códigos de Barra".'.pdf'); 
    }


    public function GetBarCodeById($code){
        $code_data = bienes_activos::select('codigo_sicoin')->where('codigo_sicoin','=',$code)->count();
        
        if($code_data == 0){
            $html = '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="X-UA-Compatible" content="ie=edge">
                    <title>INVENTARIO</title>
                    <script src="https://code.jquery.com/jquery-3.5.0.js" integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc=" crossorigin="anonymous"></script>
                    <style>
                        @page { size:2in 1in; margin: 2cm }
                        * {
                            font-size: 12px;
                            font-family: "Times New Roman";
                        }
                        
                        
                        
                        .centrado {
                            text-align: center;
                            align-content: center;
                        }
                        
                        .ticket {
                            padding:0 auto;
                            margin:0 auto;
                            width: 155px;
                            max-width: 155px;
                        }
                        
                        img {
                            max-width: inherit;
                            width: inherit;
                        }
                        </style>
                    
                
                </head>
                <body>
                    <div class="ticket">

                    </div>
                    <script>
                    </script>
                </body>
                </html>
                ';
        }else{
            $code_data = bienes_activos::select('codigo_sicoin')->where('codigo_sicoin','=',$code)->get();
            $code = ' <img src="data:image/png;base64,' . DNS1D::getBarcodePNG($code_data[0]['codigo_sicoin'], 'C128',2,80,array(0,0,0),true) . '" alt="barcode"   />';
            $imagen = DNS1D::getBarcodePNGPath($code_data[0]['codigo_sicoin'], 'C128',2,80,array(0,0,0),true);

         

            $path = public_path().$imagen;
            $img = '<img src="'.$path.'" alt="barcode" ';
            $html = '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="X-UA-Compatible" content="ie=edge">
                    <title>INVENTARIO</title>
                    
                    
                    <style>
                        @page { size:2in 0.9in; margin: 0cm}
                        * {
                            padding:0 auto;
                            font-size: 12px;
                            font-family: "Times New Roman";
                        }
                        
                        .centrado {
                            text-align: center;
                            align-content: center;
                        }
                        
                        .ticket {
                            padding:0 auto;
                            margin:0 auto;
                            width: 170px;
                            max-width: 170px;
                            padding-left: -85px;
                            padding-top:30px;
                        }
                        
                        img {
                            max-width: inherit;
                            width: inherit;
                        }
                        </style>
                    
                
                </head>
                <body>
                    <div class="ticket">
                            <img src="data:image/png;base64,' . DNS1D::getBarcodePNG($code_data[0]['codigo_sicoin'], 'C128',2,50,array(0,0,0),true) . '" alt="barcode"   />
                    </div>
                    <script>
                        
                    </script>
                </body>
                </html>';
        }
        $code = '<div class="BarCode">'. DNS1D::getBarcodeHTML($code_data[0]['codigo_sicoin'],"C128",3,110,'black',true).'</div>';
        $code2 = '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($code_data[0]['codigo_sicoin'], 'C128',3,110,array(0,0,0),true) . '" alt="barcode"  />';
        // <img src="data:image/png;base64,' . DNS1D::getBarcodePNGPath($code_data[0]['codigo_sicoin'], 'C128',2,80,array(0,0,0),true) . '" alt="barcode"   />

        $pdf = \PDF::loadHtml($html);
        $html2 = 'test';
        $paper_size = array(0,0,144,308);
        $pdf->setPaper($paper_size, 'landscape');
        // $pdf->setOptions(['dpi' => 120, 'defaultFont' => 'sans-serif']);
        return $pdf->stream("Códigos de Barra".'.pdf'); 
        // return $code2;
        
    }

    public function GetSearchCodeById($code){

        
        $code_data = bienes_activos::select('activos.id_activo','productos.descripcion','activos.codigo_sicoin','activos.fecha_ingreso', 'activos.cantidad')
                        ->join('productos','productos.id_producto','=','activos.id_producto')
                        ->where('codigo_sicoin','=',$code)->get();

        return response()->json($code_data,200);
        
    }

}
