<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\receptor;
use App\Model\sequences;
use App\Model\documento;
use App\Model\copiaDestinatarios;
use App\Model\dependencias;
use App\Model\traslados;
use App\Model\estado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class documentos extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function showDocument(){
        $usuario = $this->getdepartamentobyId();
        $usuario = json_decode(json_encode($usuario));

        return view('administrativo.showDocument',[
            'id_departamento'   =>  $usuario->original
        ]);
    }

    public function showRecibido(){
        return view('administrativo.showRecibido');
    }

    public function showCreate(){
        $usuario = $this->getdepartamentobyId();
        $usuario = json_decode(json_encode($usuario));
        
        return view('administrativo.showCreate',[
            'departamento'   => $usuario->original
        ]);
    }
    
    public function showTraslados(){
        $usuario = $this->getdepartamentobyId();
        $usuario = json_decode(json_encode($usuario));
        
        return view('administrativo.showTraslados',[
            'departamento'   => $usuario->original
        ]);
    }



    public function showReceptores(){
        return view('administrativo.showReceptores');
    }


    public function getdepartamentobyId(){
        $usuario = Auth::user()->id_unidad;
        return response()->json($usuario,200);
    }

    public function getUserbyId(){
        $usuario = Auth::user()->id;
        return response()->json($usuario,200);
    }



    public function getNamebyId($id){
        $dependencia = dependencias::where('id_dependencia', $id)->select('descripcion as name')->get();
        return response()->json($dependencia,200);
    }

    public function createDocumento(Request $request){

        // dd($request);


        // $count_copias = sizeof($request->copia);
        $count_receptor = sizeof($request->receptor);
        // if($count_copias > $count_receptor ||  $count_copias < $count_receptor || $count_copias === $count_receptor){
        if($count_receptor > 0){
            try {
                DB::beginTransaction();
    
                $documento = new documento;
                $documento->dirigido = $request->dirigido;
                $documento->id_dependencia = $request->departamento;
                $documento->descripcion = $request->cuerpo;
                $documento->direccion = $request->destinatario;
                $documento->correlativo_documento = $request->correlativo;
                $documento->save();
                $id_documento = $documento->id;
    
                for($iteration = 0; $iteration < $count_receptor; $iteration++){
                    $copias = new copiaDestinatarios;
                    $copias->id_receptor = $request->receptor[$iteration];
                    $copias->id_documento = $id_documento;
                    $copias->save();                   
                }
                DB::commit();
                return response()->json(true,200);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json($th,500);
            }
            
        }else{
                try {
                    DB::beginTransaction();
        
                    $documento = new documento;
                    $documento->dirigido = $request->dirigido;
                    $documento->id_dependencia = $request->departamento;
                    $documento->descripcion = $request->cuerpo;
                    $documento->direccion = $request->destinatario;
                    $documento->correlativo_documento = $request->correlativo;
                    $documento->save();
                    $id_documento = $documento->id;
        
                    DB::commit();
                    return response()->json(true,200);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json($th,500); 
                }
        }
    }

    public function getReceptores(){
        $data = receptor::select('receptors.id','receptors.descripcion','dependencias.descripcion as dependencia')->join('dependencias','dependencias.id_dependencia','receptors.id')->get();
        return response()->json($data,200);
    }

    

    public function getReceptoresbyId(Request $request){
        $data = receptor::select('id as code','descripcion','idDepartamento as dep')
        ->whereIn('idDepartamento',$request->direcciones)
        ->get();
        return response()->json($data,200);
    }

    public function setReceptores(Request $request){
        try {
            DB::beginTransaction();

            $receptor = new receptor;

            $receptor->descripcion = $request->name;
            $receptor->idDepartamento = $request->departamento;
            $receptor->save();

            DB::commit();

            return response()->json($receptor,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th,500);
        }
    }

    public function updateReceptor(Request $request){
        try {
            DB::beginTransaction();

            $update = receptor::where('id', $request->id)->update(['descripcion' => $request->name]);
            
            DB::commit();
            return response()->json($update,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th,500);
        }
    }

    public function listDocument(){


        try {
            DB::beginTransaction();
            $id = $this->getdepartamentobyId();
            $id = json_decode(json_encode($id));

            $documento = DB::select('SELECT 
                                            id as code,
                                            dirigido,
                                            id_dependencia,
                                            direccion as descripcion,
                                            correlativo_documento as correlativo,
                                            created_at as fecha
                                            FROM documentos
                                WHERE id_dependencia = :id AND id NOT IN 
                                    (SELECT idDocumento AS Documento FROM traslados WHERE estado = \'A\' and idDocumento IN 
                                    (SELECT id AS code_document FROM documentos WHERE id_dependencia = :idd));
            ',['id' => $id->original, 'idd' => $id->original]);

            return response()->json($documento,200);    
            
            DB::commit();
        } catch (\Throwable $th) {
            return response()->json($th,200);    
            DB::rollBack();
        }

        
        // $documento = documento::where('documentos.id_dependencia', $id->original)
        // ->select('documentos.id as code','documentos.dirigido','documentos.id_dependencia','documentos.direccion as descripcion','documentos.correlativo_documento as correlativo','documentos.created_at as fecha')
        // ->get();





        // $documento = documento::where('documentos.id_dependencia', $id->original)
        // ->select('documentos.id as code','documentos.dirigido','documentos.id_dependencia','documentos.direccion as descripcion','documentos.correlativo_documento as correlativo',
        //             'documentos.created_at as fecha',
        //             DB::raw('(CASE WHEN (traslados.estado = "A") THEN concat("Trasladado a ", dependencias.descripcion)  ELSE "Sin Enviar" END) AS estado'))
        // ->leftjoin('traslados','traslados.idDocumento','=','documentos.id')
        // ->leftjoin('dependencias','traslados.idDepartamentoActual','=','dependencias.id_dependencia')
        // ->where('traslados.estado' , '<>','A')
        // ->get();
        

    }
    public function listDocumentTransfert(){

        try {
            DB::beginTransaction();
            $id = $this->getdepartamentobyId();
            $id = json_decode(json_encode($id));

            $documento = documento::where('documentos.id_dependencia', $id->original)
            ->select('documentos.id as code','documentos.dirigido','documentos.id_dependencia','documentos.direccion as descripcion','documentos.correlativo_documento as correlativo',
                        'documentos.created_at as fecha',
                        DB::raw('(CASE WHEN (traslados.estado = "A") THEN concat("Trasladado a ", dependencias.descripcion)  ELSE "Sin Enviar" END) AS estado'))
            ->leftjoin('traslados','traslados.idDocumento','=','documentos.id')
            ->leftjoin('dependencias','traslados.idDepartamentoActual','=','dependencias.id_dependencia')
            ->where('traslados.estado' , '=','A')
            ->get();

            return response()->json($documento,200);    
            
            DB::commit();
        } catch (\Throwable $th) {
            return response()->json($th,200);    
            DB::rollBack();
        }

    }

    public function sequences_data($tabla){
        
        // verificar si es tabla vacia
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

    public function documentTransfer(Request $request){
        try {
            DB::beginTransaction();

            $usuario = $this->getUserbyId();
            $usuario = json_decode(json_encode($usuario));

            if($existe = traslados::where('idDocumento',$request->Documento)->where('estado','A')->select('id')->count() === 0){
                $vacio = true;
            }else{
                $idTraslado = traslados::where('idDocumento',$request->Documento)->where('estado','A')->select('id as code','idDepartamentoActual as name')->get();
                $idTraslado = json_decode(json_encode($idTraslado));

                $idTransfer = $idTraslado[0]->code;
                $idDepTransfer = $idTraslado[0]->name;
                $vacio = false;
            } 

            if($vacio === true){
                $traslado = new traslados;
                $traslado->idDocumento = $request->Documento;
                $traslado->idDepartamentoActual = $request->idDireccionTraslado;
                $traslado->idDepartamentoTraslado = $request->actual;
                $traslado->idUsuarioTramito = $usuario->original;
                $traslado->estado = 'A';
                $traslado->save();
                $IdTrasladoNuevo = $traslado->id;

                $this->setEstadoTransfer($IdTrasladoNuevo,$request->idDireccionTraslado);
                DB::commit();
                return response()->json($traslado,200);
            }else{
                $update = traslados::where('id',$idTransfer)->update(['estado' => 'I']);                
                $traslados = new traslados;
                $traslados->idDocumento = $request->Documento;
                $traslados->idDepartamentoActual = $request->idDireccionTraslado;
                $traslados->idDepartamentoTraslado = $idDepTransfer;
                $traslados->idUsuarioTramito = $usuario->original;
                $traslados->estado = 'A';
                $traslados->save();
                $IdTrasladoNuevo = $traslados->id;
                $this->setEstadoTransfer($IdTrasladoNuevo,$request->idDireccionTraslado);
                // dd('insert');
                DB::commit();
                return response()->json($traslados,200);
            }



        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th,200);
        }
    }


    public function retornar(Request $request){
        try {
            DB::beginTransaction();

            $usuario = $this->getUserbyId();
            $usuario = json_decode(json_encode($usuario));

            if($existe = traslados::where('idDocumento',$request->Documento)->where('estado','A')->select('id')->count() === 0){
                $vacio = true;
            }else{
                $idTraslado = traslados::where('idDocumento',$request->Documento)->where('estado','A')->select('id as code','idDepartamentoActual as name')->get();
                $idTraslado = json_decode(json_encode($idTraslado));

                $idTransfer = $idTraslado[0]->code;
                $idDepTransfer = $idTraslado[0]->name;
                $vacio = false;
            } 

            if($vacio === true){
                $traslado = new traslados;
                $traslado->idDocumento = $request->Documento;
                $traslado->idDepartamentoActual = $request->idDireccionTraslado;
                $traslado->idDepartamentoTraslado = $request->actual;
                $traslado->idUsuarioTramito = $usuario->original;
                $traslado->estado = 'A';
                $traslado->save();
                $IdTrasladoNuevo = $traslado->id;

                $this->setEstadoTransfer($IdTrasladoNuevo,$request->idDireccionTraslado);
                DB::commit();
                return response()->json($traslado,200);
            }else{
                $update = traslados::where('id',$idTransfer)->update(['estado' => 'I']);                
                $traslados = new traslados;
                $traslados->idDocumento = $request->Documento;
                $traslados->idDepartamentoActual = $request->idDireccionTraslado;
                $traslados->idDepartamentoTraslado = $idDepTransfer;
                $traslados->idUsuarioTramito = $usuario->original;
                $traslados->estado = 'A';
                $traslados->save();
                $IdTrasladoNuevo = $traslados->id;
                $this->setEstadoTransfer($IdTrasladoNuevo,$request->idDireccionTraslado);
                // dd('insert');
                DB::commit();
                return response()->json($traslados,200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th,200);
        }

    }

    public function setEstadoTransfer($idTraslado,$idDepartamento){ 
        $estado = new estado;

        $estado->idTraslado = $idTraslado;
        $estado->idDepartamento = $idDepartamento;
        $estado->estado = 'I';
        $estado->save();

        return response()->json(true,200);
    }

    public function getRecepcion(){
        $usuario = $this->getdepartamentobyId();
        $usuario = json_decode(json_encode($usuario));

        // $data = estado::where('idDepartamento',$usuario->original)->where('estado','I')->select('id')->count();
        $mensaje = estado::select('estado.id as code','documentos.dirigido','documentos.direccion','documentos.correlativo_documento','documentos.created_at','estado.estado')
        ->join('traslados','estado.idTraslado','=','traslados.id')
        ->join('documentos','traslados.idDocumento','=','documentos.id')
        ->where('idDepartamento',$usuario->original)
        // ->where('estado.estado','A')
        // ->where('estado.estado','I')
        ->get();        
        return response()->json($mensaje,200);
    }

    public function getRecepcionMessage(){
        $usuario = $this->getdepartamentobyId();
        $usuario = json_decode(json_encode($usuario));

        $mensaje = estado::select('estado.id as code','documentos.dirigido','documentos.direccion','documentos.correlativo_documento','documentos.created_at')
        ->join('traslados','estado.idTraslado','=','traslados.id')
        ->join('documentos','traslados.idDocumento','=','documentos.id')
        ->where('idDepartamento',$usuario->original)
        ->where('estado.estado','I')
        ->get();        
        return response()->json($mensaje,200);
    }


    public function toAccept(Request $request){
       
        try {
            DB::beginTransaction();
            $update = estado::where('id',$request->code)->update(['estado' => 'A']);
            DB::commit();
            return response()->json($update,200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th,200);
        }
    }
}
