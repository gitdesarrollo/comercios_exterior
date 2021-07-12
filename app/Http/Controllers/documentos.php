<?php

namespace App\Http\Controllers;

use App\Mail\receiptOfNotification;
use App\Mail\copiesUser;
use Illuminate\Http\Request;
use App\Model\receptor;
use App\Model\sequences;
use App\Model\documento;
use App\Model\copiaDestinatarios;
use App\Model\dependencias;
use App\Model\traslados;
use App\Model\estado;
use App\Model\profesiones;
use App\Model\comentarios;
use App\User;
use App\Model\user_has_view;
use App\Model\userHasRoles;
use App\Model\trasladoExterno;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Model\typeDocument;
use App\Model\uploadFile;
use App\Model\setting;
use App\Model\tracing;
use App\Model\vice_has_dep;
use App\Model\withCopy;

use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;
use PDF;

class documentos extends Controller
{

    public function __construct(){
        ini_set('max_execution_time', 3500);
        $this->middleware('auth');
    }

    public function showDocument(){
        $usuario = $this->getdepartamentobyId();
        $usuario = json_decode(json_encode($usuario));

        $permiso = $this->getPermissionById(3);
        if($permiso->original[0]['admin']){
            return view('administrativo.showDocument',[
                'id_departamento'   =>  $usuario->original
            ]);
        }elseif($permiso->original[0]['permit']){
            return view('administrativo.showDocument',[
                'id_departamento'   =>  $usuario->original
            ]);
        }
        else{
            return header( "refresh:0.1;url=/" );
            // return view('admin.home');
        }

    }

    public function getPermissionById($vista){
        $isAdmin = Auth::user()->admin;
        $idUser = Auth::user()->id;

        $rol = userHasRoles::where(['idUser' => $idUser ])->select('idRoles')->get();
        $permit = user_has_view::where(['rol' => $rol[0]->idRoles, 'permits' => $vista])
                ->select('estado')->count();
        $permisos = [];
        if($isAdmin == 1){
            array_push($permisos,[
                    "admin"     =>  true,
                    "permit"    =>  true
            ]);
        }else{
            if($permit > 0){
                array_push($permisos,[
                        "admin"     =>  false,
                        "permit"    =>  true
                ]);
            }else{
                array_push($permisos,[
                        "admin"     =>  false,
                        "permit"    =>  false
                ]);
            }
        }
        return response()->json($permisos,200);
    }

    public function showRecibido(){
        $permiso = $this->getPermissionById(2);
        if($permiso->original[0]['admin'] ){
            return view('administrativo.showRecibido');
        }elseif($permiso->original[0]['permit']){
            return view('administrativo.showRecibido');
        }
        else{
            // return view('admin.home');
            return header( "refresh:0.1;url=/" );
        }
        
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

    public function getDependenciabyId(){
        $usuario = Auth::user()->id_unidad;
        return response()->json($usuario,200);
    }

    public function getViceHasDeps($id){
        
        $union = vice_has_dep::where(['viceministro' => $id->original])->select('viceministerios')->get();
        return response()->json($union,200);
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
                $documento->idProfesion = $request->profesion;
                $documento->id_status = 1;
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
                    $documento->idProfesion = $request->profesion;
                    $documento->id_status = 1;
                    $documento->save();
                    $id_documento = $documento->id;

                    DB::commit();
                    return response()->json(true,200);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json(false,500);
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
            $receptor->idEstado = 4;
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

    public function listDocumentRemitente(){


        try {
            DB::beginTransaction();
            $id = $this->getDependenciabyId();
            $id = json_decode(json_encode($id));

            $user = $this->getUserbyId();
            $user = json_decode(json_encode($user));

            $departamento = $this->getViceHasDeps($user);
            $departamento = json_decode(json_encode($departamento));

            $ministro = sizeof($departamento->original);

            

            if($ministro > 0){
                $documento = DB::select('
                SELECT distinct
                    rem.id,
                    rem.descripcion
                    FROM viceministerios vice
                    INNER JOIN dependencias dep
                        ON dep.idVice = vice.id
                    INNER JOIN users us
                        ON dep.id_dependencia = us.id_unidad
                    INNER JOIN traslados tras
                        ON tras.idUsuarioTramito = us.id
                    INNER JOIN documentos doc 
                        ON tras.idDocumento = doc.id
                    INNER JOIN estado es
                        ON es.idTraslado = tras.id
                    INNER JOIN estado_documentos est
                        ON est.id = es.estatus
                    INNER JOIN vice_has_deps viceHasDeps
                        ON viceHasDeps.viceministerios = vice.id
                    INNER JOIN type_documents td
                        ON td.id = doc.idTipoDocumento
                    INNER JOIN upload_files files
                        ON doc.id = files.evento_id
                    INNER JOIN remitentes rem
                        ON rem.descripcion = doc.interesado
                    WHERE vice.id = :unidad AND es.estatus = 4
                    order by rem.id asc
                ',['unidad' => $departamento->original[0]->viceministerios]);
            }else{
                $documento = DB::select('
                SELECT distinct
                    rem.id,
                    rem.descripcion
                    FROM viceministerios vice
                    INNER JOIN dependencias dep
                        ON dep.idVice = vice.id
                    INNER JOIN users us
                        ON dep.id_dependencia = us.id_unidad
                    INNER JOIN traslados tras
                        ON tras.idUsuarioTramito = us.id
                    INNER JOIN documentos doc 
                        ON tras.idDocumento = doc.id
                    INNER JOIN estado es
                        ON es.idTraslado = tras.id
                    INNER JOIN estado_documentos est
                        ON est.id = es.estatus
                    INNER JOIN type_documents td
                        ON td.id = doc.idTipoDocumento
                    INNER JOIN upload_files files
                        ON doc.id = files.evento_id
                    INNER JOIN remitentes rem
                        ON rem.descripcion = doc.interesado
                    WHERE dep.id_dependencia = :id AND es.estatus = 4
                    order by rem.id asc
                    ',['id' => $id->original]);
            }
            
            DB::commit();

            return response()->json($documento,200);

        } catch (\Throwable $th) {
            return response()->json($th,200);
            DB::rollBack();
        }

    }
    public function listDocumentAll(){


        try {
            DB::beginTransaction();
            $id = $this->getDependenciabyId();
            $id = json_decode(json_encode($id));

            $user = $this->getUserbyId();
            $user = json_decode(json_encode($user));

            $departamento = $this->getViceHasDeps($user);
            $departamento = json_decode(json_encode($departamento));

            $ministro = sizeof($departamento->original);

            

            if($ministro > 0){
                $documento = DB::select('
                SELECT distinct
                    doc.id as code,
                    doc.interesado AS empresa,
                    doc.correlativo_documento AS correlativo,
                    doc.correlativo_externo AS externo,
                    doc.descripcion as descripcion,
                    est.descripcion AS estado,
                    us.NAME AS usuario,
                    doc.created_at as fecha,
                    tras.id as idTraslado,
                    dep.descripcion AS unidad,
                    td.descripcion AS tipo,
                    CONCAT("./../files/",files.`file`) AS url
                    FROM viceministerios vice
                    INNER JOIN dependencias dep
                        ON dep.idVice = vice.id
                    INNER JOIN users us
                        ON dep.id_dependencia = us.id_unidad
                    INNER JOIN traslados tras
                        ON tras.idUsuarioTramito = us.id
                    INNER JOIN documentos doc 
                        ON tras.idDocumento = doc.id
                    INNER JOIN estado es
                        ON es.idTraslado = tras.id
                    INNER JOIN estado_documentos est
                        ON est.id = es.estatus
                    INNER JOIN vice_has_deps viceHasDeps
                        ON viceHasDeps.viceministerios = vice.id
                    INNER JOIN type_documents td
                        ON td.id = doc.idTipoDocumento
                    INNER JOIN upload_files files
                        ON doc.id = files.evento_id
                    WHERE vice.id = :unidad AND es.estatus = 4 and files.formato = "pdf"
                    order by doc.created_at desc
                ',['unidad' => $departamento->original[0]->viceministerios]);
            }else{
                $documento = DB::select('
                SELECT distinct
                    doc.id as code,
                    doc.interesado AS empresa,
                    doc.correlativo_documento AS correlativo,
                    doc.correlativo_externo AS externo,
                    doc.descripcion as descripcion,
                    est.descripcion AS estado,
                    us.NAME AS usuario,
                    doc.created_at as fecha,
                    tras.id as idTraslado,
                    dep.descripcion AS unidad,
                    td.descripcion AS tipo,
                    CONCAT("./../files/",files.`file`) AS url
                    FROM viceministerios vice
                    INNER JOIN dependencias dep
                        ON dep.idVice = vice.id
                    INNER JOIN users us
                        ON dep.id_dependencia = us.id_unidad
                    INNER JOIN traslados tras
                        ON tras.idUsuarioTramito = us.id
                    INNER JOIN documentos doc 
                        ON tras.idDocumento = doc.id
                    INNER JOIN estado es
                        ON es.idTraslado = tras.id
                    INNER JOIN estado_documentos est
                        ON est.id = es.estatus
                    INNER JOIN type_documents td
                        ON td.id = doc.idTipoDocumento
                    INNER JOIN upload_files files
                        ON doc.id = files.evento_id
                    WHERE dep.id_dependencia = :id AND es.estatus = 4 and files.formato = "pdf"
                    order by doc.created_at desc
                    ',['id' => $id->original]);
            }
            
            DB::commit();

            return response()->json($documento,200);

        } catch (\Throwable $th) {
            return response()->json($th,200);
            DB::rollBack();
        }

    }

    public function getDireccionesByUser(){

        $id = $this->getDependenciabyId();
        $id = json_decode(json_encode($id));


        $dependencias = dependencias::select('idVice as code')->where(['id_dependencia' => $id->original])->get();
        $dep = dependencias::select('id_dependencia as code','descripcion as name')->where(['idVice' => $dependencias[0]->code])->get();

        return response()->json($dep,200);
    }


    public function listByFilter(Request $request){
        
        
        $html = "";
        $bandera = false;

        if(!is_null($request->sender)){
            $html .= ' and doc.interesado = "' . $request->sender . '"';
        }
        
        if(!is_null($request->correlative)){
            $html .= ' and doc.correlativo_documento like "%' . $request->correlative . '%"';
        }
        
        if(!is_null($request->internal)){
            $html .= ' and doc.correlativo_externo like "%' . $request->internal . '%"';
        }
        
        if(!is_null($request->assigned)){
            $html .= ' and us.NAME like "%' . $request->assigned . '%"';
        }
        
        if(!is_null($request->type)){
            $html .= ' and td.id = ' . $request->type;
        }
        
        if(!is_null($request->Idate) && !is_null($request->Fdate)){
            $html .= ' and CAST(doc.created_at as DATE)  BETWEEN  CAST("' . $request->Idate . '" as DATE) and CAST("' . $request->Fdate . '" as DATE)' ;
        }
        
        if(!is_null($request->direccion)){
            $html .= ' and dep.id_dependencia = ' . $request->direccion;
            $bandera = true;
        }

        
        try {
            DB::beginTransaction();
            $id = $this->getDependenciabyId();
            $id = json_decode(json_encode($id));

            $user = $this->getUserbyId();
            $user = json_decode(json_encode($user));

            $departamento = $this->getViceHasDeps($user);
            $departamento = json_decode(json_encode($departamento));

            $ministro = sizeof($departamento->original);

            

            if($ministro > 0){
                $documento = DB::select('
                SELECT distinct
                    doc.id as code,
                    doc.interesado AS empresa,
                    doc.correlativo_documento AS correlativo,
                    doc.correlativo_externo AS externo,
                    doc.descripcion as descripcion,
                    est.descripcion AS estado,
                    us.NAME AS usuario,
                    doc.created_at as fecha,
                    tras.id as idTraslado,
                    dep.descripcion AS unidad,
                    td.descripcion AS tipo,
                    CONCAT("./../files/",files.`file`) AS url
                    FROM viceministerios vice
                    INNER JOIN dependencias dep
                        ON dep.idVice = vice.id
                    INNER JOIN users us
                        ON dep.id_dependencia = us.id_unidad
                    INNER JOIN traslados tras
                        ON tras.idUsuarioTramito = us.id
                    INNER JOIN documentos doc 
                        ON tras.idDocumento = doc.id
                    INNER JOIN estado es
                        ON es.idTraslado = tras.id
                    INNER JOIN estado_documentos est
                        ON est.id = es.estatus
                    INNER JOIN vice_has_deps viceHasDeps
                        ON viceHasDeps.viceministerios = vice.id
                    INNER JOIN type_documents td
                        ON td.id = doc.idTipoDocumento
                    INNER JOIN upload_files files
                        ON doc.id = files.evento_id
                    WHERE vice.id = :unidad AND es.estatus = 4 
                ' . $html
                ,['unidad' => $departamento->original[0]->viceministerios]);
            }else{
                if($bandera){
                    $documento = DB::select('
                    SELECT distinct
                        doc.id as code,
                        doc.interesado AS empresa,
                        doc.correlativo_documento AS correlativo,
                        doc.correlativo_externo AS externo,
                        doc.descripcion as descripcion,
                        est.descripcion AS estado,
                        us.NAME AS usuario,
                        doc.created_at as fecha,
                        tras.id as idTraslado,
                        dep.descripcion AS unidad,
                        td.descripcion AS tipo,
                        CONCAT("./../files/",files.`file`) AS url
                        FROM viceministerios vice
                        INNER JOIN dependencias dep
                            ON dep.idVice = vice.id
                        INNER JOIN users us
                            ON dep.id_dependencia = us.id_unidad
                        INNER JOIN traslados tras
                            ON tras.idUsuarioTramito = us.id
                        INNER JOIN documentos doc 
                            ON tras.idDocumento = doc.id
                        INNER JOIN estado es
                            ON es.idTraslado = tras.id
                        INNER JOIN estado_documentos est
                            ON est.id = es.estatus
                        INNER JOIN type_documents td
                            ON td.id = doc.idTipoDocumento
                        INNER JOIN upload_files files
                            ON doc.id = files.evento_id
                        WHERE es.estatus = 4 
                        ' . $html);
                }else {
                    $documento = DB::select('
                    SELECT distinct
                        doc.id as code,
                        doc.interesado AS empresa,
                        doc.correlativo_documento AS correlativo,
                        doc.correlativo_externo AS externo,
                        doc.descripcion as descripcion,
                        est.descripcion AS estado,
                        us.NAME AS usuario,
                        doc.created_at as fecha,
                        tras.id as idTraslado,
                        dep.descripcion AS unidad,
                        td.descripcion AS tipo,
                        CONCAT("./../files/",files.`file`) AS url
                        FROM viceministerios vice
                        INNER JOIN dependencias dep
                            ON dep.idVice = vice.id
                        INNER JOIN users us
                            ON dep.id_dependencia = us.id_unidad
                        INNER JOIN traslados tras
                            ON tras.idUsuarioTramito = us.id
                        INNER JOIN documentos doc 
                            ON tras.idDocumento = doc.id
                        INNER JOIN estado es
                            ON es.idTraslado = tras.id
                        INNER JOIN estado_documentos est
                            ON est.id = es.estatus
                        INNER JOIN type_documents td
                            ON td.id = doc.idTipoDocumento
                        INNER JOIN upload_files files
                            ON doc.id = files.evento_id
                        WHERE dep.id_dependencia = :id AND es.estatus = 4 
                        ' . $html
                        ,['id' => $id->original]);
                }
            }
            
            DB::commit();

            // dd($documento);
            return response()->json($documento,200);

        } catch (\Throwable $th) {
            return response()->json($th,200);
            DB::rollBack();
        }

    }

    public function listDocument(){


        try {
            DB::beginTransaction();
            $id = $this->getUserbyId();
            $id = json_decode(json_encode($id));

            $documento = DB::select('SELECT
                                        d.id as code,
                                        d.interesado AS empresa,
                                        d.correlativo_documento AS correlativo,
                                        d.descripcion as descripcion,
                                        ed.descripcion AS estado,
                                        us.NAME AS usuario,
                                        d.created_at as fecha
                                        FROM documentos d
                                        INNER JOIN traslados tras
                                            ON d.id = tras.id
                                        INNER JOIN estado_documentos ed
                                        ON tras.estado = ed.id
                                        INNER JOIN users us
                                            ON tras.idUsuarioTramito = us.id
                                        WHERE tras.estado = 2 AND us.id = :id
            ',['id' => $id->original]);
            DB::commit();

            return response()->json($documento,200);

        } catch (\Throwable $th) {
            return response()->json($th,200);
            DB::rollBack();
        }

            // $documento = DB::select('SELECT
            //                                 id as code,
            //                                 dirigido,
            //                                 id_dependencia,
            //                                 direccion as descripcion,
            //                                 correlativo_documento as correlativo,
            //                                 created_at as fecha
            //                                 FROM documentos
            //                     WHERE id_dependencia = :id AND id NOT IN
            //                         (SELECT idDocumento AS Documento FROM traslados WHERE estado = \'A\' and idDocumento IN
            //                         (SELECT id AS code_document FROM documentos WHERE id_dependencia = :idd));
            // ',['id' => $id->original, 'idd' => $id->original]);



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
                        'documentos.created_at as fecha','traslados.id as idTraslado',
                        DB::raw('(CASE WHEN (traslados.estado in(2,6)) THEN concat("Trasladado a ", dependencias.descripcion)  ELSE "Sin Enviar" END) AS estado'))
            ->leftjoin('traslados','traslados.idDocumento','=','documentos.id')
            ->leftjoin('dependencias','traslados.idDepartamentoActual','=','dependencias.id_dependencia')
            // ->where('traslados.estado' , '=',2)
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

            if($existe = traslados::where('idDocumento',$request->Documento)->where('estado',2)->select('id')->count() === 0){
                $vacio = true;
            }else{
                $idTraslado = traslados::where('idDocumento',$request->Documento)->where('estado',2)->select('id as code','idDepartamentoActual as name')->get();
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
                $traslado->estado = 2;
                $traslado->save();
                $IdTrasladoNuevo = $traslado->id;

                $this->setEstadoTransfer($IdTrasladoNuevo,$request->idDireccionTraslado);

                /**Update status document transfer */
                $update = documento::where('id',$request->Documento)->update(['id_status' => 2]);
                /********************************* */
                DB::commit();
                return response()->json($traslado,200);
            }else{
                $update = traslados::where('id',$idTransfer)->update(['estado' => 5]);
                $traslados = new traslados;
                $traslados->idDocumento = $request->Documento;
                $traslados->idDepartamentoActual = $request->idDireccionTraslado;
                $traslados->idDepartamentoTraslado = $idDepTransfer;
                $traslados->idUsuarioTramito = $usuario->original;
                $traslados->estado = 2;
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


        // try {
        //         DB::beginTransaction();
                $usuario = $this->getUserbyId();
                $usuario = json_decode(json_encode($usuario));
                $idTraslado = DB::select('SELECT id as code, idDepartamentoActual as name, idDocumento as documento
                    FROM traslados
                    WHERE id = :id and estado in (2,6)
                ',['id' => $request->Documento]);
                $idTransfer = $idTraslado[0]->code;
                $idDepTransfer = $idTraslado[0]->name;
                $idDocumento = $idTraslado[0]->documento;

                $update = traslados::where('id',$idTransfer)->update(['estado' => 5]);
                $traslados = new traslados;
                $traslados->idDocumento = $idDocumento;
                $traslados->idDepartamentoActual = $request->idDireccionTraslado;
                $traslados->idDepartamentoTraslado = $idDepTransfer;
                $traslados->idUsuarioTramito = $usuario->original;
                $traslados->estado = 2;
                $traslados->save();
                $IdTrasladoNuevo = $traslados->id;
                $this->setEstadoTransfer($IdTrasladoNuevo,$request->idDireccionTraslado);
                // DB::commit();
                return response()->json($traslados,200);
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     return response()->json($th,200);
        // }

    }
    public function setTransferInt(Request $request){


        try {
            DB::beginTransaction();
            $idUsuario = $this->getUserbyId();
            $idUsuario = json_decode(json_encode($idUsuario));

            if($request->externo){
                $trasladoU = traslados::where('idUsuarioTramito',$idUsuario->original)->select('id')->count();

                if($trasladoU > 0){


                    
                    $trasladoUs = traslados::where('idUsuarioTramito',$idUsuario->original)->select('id')->get();
                    $idTranfer = $trasladoUs[0]->id;

                    $usuario = $this->getUserbyId();
                    $usuario = json_decode(json_encode($usuario));

                    $idTraslado = DB::select('SELECT id as code, idUsuarioTramito as name, idDocumento as documento, estado
                        FROM traslados
                        WHERE id = :id and estado = 3
                    ',['id' => $request->Documento]);
                    $idTransfer = $idTraslado[0]->code;
                    $UsuarioTraslado = $idTraslado[0]->name;
                    $idDocumentoTraslado = $idTraslado[0]->documento;
                    $estadoTraslado = $idTraslado[0]->estado;
                    $estados = DB::select('SELECT estadoAnterior as anterior, estadoActual as actual FROM estado WHERE idTraslado = :id and estatus = 4 and estadoActual = 3',['id' => $idTransfer]);
                    $idAnterior = $estados[0]->actual;

                    $update = traslados::where('id',$idTransfer)->update(['estado' => 9 ,'idUsuarioTramito' => $idUsuario->original]);
                    $updateEstado = estado::where(['idTraslado' => $idTransfer,'estatus' => 4])->update(['estatus' => 5]);
                    // $updateEstado = estado::where(['idTraslado' => $idTransfer,'estatus' => 4, 'estadoActual','!=' => 8])->update(['estatus' => 5]);
                    $estado = new estado;
                    $estado->idTraslado = $idTransfer;
                    $estado->estadoAnterior = $idAnterior;
                    $estado->estadoActual = 9;
                    $estado->estatus = 4;
                    $estado->UsuarioActual = $idUsuario->original;
                    $estado->save();

                    // dd($idTransfer);

                    $TrasladoExterno = new trasladoExterno;
                    $TrasladoExterno->traslado_id = $idTransfer;
                    $TrasladoExterno->lugar_destino = $request->destino;
                    $TrasladoExterno->correlativo_salida = $request->correlativoEx;
                    $TrasladoExterno->save();

                    $usuarioTo = User::where('id',$idUsuario->original)->select('name','email')->get();

                    $documentoTo = documento::where('id',$idDocumentoTraslado)->select('interesado','correlativo_documento','descripcion','correlativo_externo')->get();

                    $empresa_to_document = $documentoTo[0]->interesado;
                    $correlativo_to_document = $documentoTo[0]->correlativo_documento;
                    $descripcion_to_document = $documentoTo[0]->descripcion;
                    $externo_to_document = $documentoTo[0]->correlativo_externo;

                    $to_name = $usuarioTo[0]->name;
                    $to_email = $usuarioTo[0]->email;
                    // $to_email = 'jjolong@miumg.edu.gt';
                    // $to_email = 'mahernandez@mineco.gob.gt';
                    $to_empresa = $empresa_to_document;
                    $to_numero = $correlativo_to_document;
                    $to_asunto = $descripcion_to_document;
                    $subject = 'Traslado Externo a: ' . $request->destino . ' con numero de correlativo: ' . $request->correlativoEx;
                    $externo = $externo_to_document;

                    $parametro = setting::where(['commandString' => 'SHOW_EMAIL'])->select('valuesString')->get();
                    $flag = $parametro[0]->valuesString;

                    if($flag == "true"){
                        Mail::to($to_email)->send(new NotificationMail($to_name,$to_empresa,$to_numero,$to_asunto,$subject,$externo), function ($message){
    
                            $message->from('jjolon@correo.com','envio');
                        });
                    }


                    // DB::commit();
                    return response()->json($update,200);
                }
            }else{

                $trasladoU = traslados::where('idUsuarioTramito',$idUsuario->original)->select('id')->count();
                $tacing = tracing::where(['id' => $request->tracing])->update(['idUsuarioActual' => $request->idUsuario]);

                if($trasladoU > 0){
                    // datos no utilizables
                    $trasladoUs = traslados::where('idUsuarioTramito',$idUsuario->original)->select('id')->get();
                    $idTranfer = $trasladoUs[0]->id;
                    $usuario = $this->getUserbyId();
                    $usuario = json_decode(json_encode($usuario));

                    

                    $idTraslado = DB::select('SELECT id as code, idUsuarioTramito as name, idDocumento as documento, estado
                        FROM traslados
                        WHERE id = :id and estado = 3
                    ',['id' => $request->Documento]);

                    $idTransfer = $idTraslado[0]->code;
                    $UsuarioTraslado = $idTraslado[0]->name;
                    $idDocumentoTraslado = $idTraslado[0]->documento;
                    $estadoTraslado = $idTraslado[0]->estado;

                    $estados = DB::select('SELECT estadoAnterior as anterior, estadoActual as actual FROM estado WHERE idTraslado = :id and estatus = 4 and estadoActual = 3',['id' => $idTransfer]);
                    $idAnterior = $estados[0]->actual;

                    

                    $update = traslados::where('id',$idTransfer)->update(['estado' => 2 ,'idUsuarioTramito' => $request->idUsuario]);
                    $updateEstado = estado::where(['idTraslado' => $idTransfer,'estatus' => 4])->update(['estatus' => 5]);
                    // $updateEstado = estado::where(['idTraslado' => $idTransfer,'estatus' => 4, 'estadoActual','!=' => 8])->update(['estatus' => 5]);
                    $estado = new estado;
                    $estado->idTraslado = $idTransfer;
                    $estado->estadoAnterior = $idAnterior;
                    $estado->estadoActual = 8;
                    $estado->estatus = 4;
                    $estado->UsuarioActual = $request->idUsuario;
                    $estado->save();

                    $id_copia = [];
                    foreach($request->copy as $item){
                        $copy = new withCopy;
                        $copy->document_id = $request->Documento;
                        $copy->user_id = $item;
                        $copy->save();
                        
                        array_push($id_copia,[
                            "code"  => $copy->id
                        ]);


                    }

                    

                    $email_copy_info = [];
                    foreach($id_copia as $row){
                        $email_copy = withCopy::select('users.email as correo','users.name as user')
                            ->join('documentos','with_copies.document_id','=','documentos.id')
                            ->join('users','with_copies.user_id','=','users.id')
                            ->where(['with_copies.document_id' => $request->Documento, 'with_copies.id' => $row['code']])
                            ->get();
                        array_push($email_copy_info,[
                            "correo"    => $email_copy[0]->correo,
                            "user"      => $email_copy[0]->user
                        ]);
                        
                    }

                    

                    
                    $usuarioTo = User::where('id',$request->idUsuario)->select('name','email')->get();

                    $documentoTo = documento::where('id',$idDocumentoTraslado)->select('interesado','correlativo_documento','descripcion','correlativo_externo')->get();


                   


                    $empresa_to_document = $documentoTo[0]->interesado;
                    $correlativo_to_document = $documentoTo[0]->correlativo_documento;
                    $descripcion_to_document = $documentoTo[0]->descripcion;
                    $externo_to_document = $documentoTo[0]->correlativo_externo;

                    $to_name = $usuarioTo[0]->name;
                    $to_email = $usuarioTo[0]->email;
                    // $to_email = 'jjolong@miumg.edu.gt';
                    // $to_email = 'mahernandez@mineco.gob.gt';
                    $to_empresa = $empresa_to_document;
                    $to_numero = $correlativo_to_document;
                    $to_asunto = $descripcion_to_document;
                    $subject = 'Traslado de Documento';
                    $externo = $externo_to_document;

                    $parametro = setting::where(['commandString' => 'SHOW_EMAIL'])->select('valuesString')->get();
                    $flag = $parametro[0]->valuesString;

                    if($flag == "true"){
                        Mail::to($to_email)->send(new NotificationMail($to_name,$to_empresa,$to_numero,$to_asunto,$subject,$externo), function ($message){
    
                            $message->from($to_email,'envio');
                        });
                    }

                    $instrucciones = tracing::select('instruccion','fechaFinal')->where(['idDocumento' => $request->Documento])->get();

                    
                    if(!$instrucciones->isEmpty()){
                        foreach($email_copy_info as $correo){
                            
                            if($flag == "true"){
                                Mail::to($correo['correo'])->send(new copiesUser($correo['user'],$to_empresa,$to_numero,$to_asunto,$subject,$externo,$email_copy_info,$instrucciones[0]->instruccion,$instrucciones[0]->fechaFinal), function ($message){
                                    $message->from($correo['correo'],'envio');
                                });
                            }
                        }
                    }else{


                        $instruccion = '';
                        $fechaFinal = '';
                        foreach($email_copy_info as $correo){
                            
                            if($flag == "true"){
                                Mail::to($correo['correo'])->send(new copiesUser($correo['user'],$to_empresa,$to_numero,$to_asunto,$subject,$externo,$email_copy_info,$instruccion,$fechaFinal), function ($message){
                                    $message->from($correo['correo'],'envio');
                                });
                            }
                        }
                    }
                    


                    DB::commit();
                    return response()->json($instrucciones,200);
                }

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
        $estado->estado = 5;
        $estado->save();

        return response()->json(true,200);
    }

    public function getRecepcionMesssage(){


        $usuario = $this->getdepartamentobyId();
        $usuario = json_decode(json_encode($usuario));

        $idUsuario = $this->getUserbyId();
        $idUsuario = json_decode(json_encode($idUsuario));

        

        $trasladoU = traslados::where('idUsuarioTramito',$idUsuario->original)->select('id')->count();


        if($trasladoU > 0){
            
            $trasladoUs = traslados::where('idUsuarioTramito',$idUsuario->original)->select('id')->get();
            $idTranfer = $trasladoUs[0]->id;
            $documento = DB::select("SELECT
            d.id as code,
            d.interesado AS empresa,
            d.correlativo_documento AS correlativo,
            d.descripcion as descripcion,
            ed.id AS estado,
            us.NAME AS usuario,
            d.created_at as fecha,
            tras.id as idTraslado,
            rol.idRoles as rol,
            d.correlativo_externo as formato,
            d.tracing,
            (SELECT id FROM tracings WHERE idDocumento = d.id AND estado in (4,5)) AS idTracing,
            (SELECT 
			(CASE 
				WHEN idUsuarioTraslada = :local THEN 'true'
				ELSE 'false'
				END) AS flag 
                FROM tracings WHERE idDocumento = d.id AND estado in (4,5)) AS flag,
            concat('./../files/',files.`file`) AS url
            FROM documentos d
            INNER JOIN traslados tras
                ON d.id = tras.id
            INNER JOIN estado_documentos ed
            ON tras.estado = ed.id
            INNER JOIN users us
                ON tras.idUsuarioTramito = us.id
            INNER JOIN user_has_roles rol
                ON us.id = rol.idUser
            INNER JOIN upload_files files
   		        ON files.evento_id = d.id
            WHERE us.id = :id  AND d.id_status != 7 AND files.formato = 'pdf' AND ed.id = 2
            ",['id' => $idUsuario->original, 'local' =>$idUsuario->original]);

            

            return response()->json($documento,200);
        }
                         // $mensaje = estado::select(
                         //     'estado.id as code',
                         //     'documentos.dirigido',
                         //     'documentos.direccion',
                         //     'documentos.correlativo_documento',
                         //     'documentos.created_at',
                         //     'estado.estado',
                         //     'traslados.id as traslado',
                         //     'documentos.id as documento')
                         // ->join('traslados','estado.idTraslado','=','traslados.id')
                         // ->join('documentos','traslados.idDocumento','=','documentos.id')
                         // ->where('traslados.id',$idTranfer)
                         // ->where('traslados.estado',6)
                         // ->where('estado.estado','I')
        // }else{
        //     $documento = DB::select('SELECT
        //                                 d.id as code,
        //                                 d.interesado AS empresa,
        //                                 d.correlativo_documento AS correlativo,
        //                                 d.descripcion as descripcion,
        //                                 ed.id AS estado,
        //                                 us.NAME AS usuario,
        //                                 d.created_at as fecha,
        //                                 tras.id as idTraslado
        //                                 FROM documentos d
        //                                 INNER JOIN traslados tras
        //                                     ON d.id = tras.id
        //                                 INNER JOIN estado_documentos ed
        //                                 ON tras.estado = ed.id
        //                                 INNER JOIN users us
        //                                     ON tras.idUsuarioTramito = us.id
        //                                 WHERE us.id = :id
        //     ',['id' => $idUsuario->original]);
        //     // $mensaje = estado::select('estado.id as code','documentos.dirigido','documentos.direccion','documentos.correlativo_documento','documentos.created_at','estado.estado','traslados.id as traslado','documentos.id as documento')
        //     // ->join('traslados','estado.idTraslado','=','traslados.id')
        //     // ->join('documentos','traslados.idDocumento','=','documentos.id')
        //     // ->where('idDepartamento',$usuario->original)
        //     // ->where('traslados.estado',2)
        //     // // ->where('estado.estado','I')
        //     // ->get();
        //     return response()->json($documento,200);
        // }

        // $data = estado::where('idDepartamento',$usuario->original)->where('estado','I')->select('id')->count();
    }

    public function getWithCopies(){
        $idUsuario = $this->getUserbyId();
        $idUsuario = json_decode(json_encode($idUsuario));

        $query = DB::select("SELECT 
                doc.id as code,
                doc.interesado as empresa,
                doc.correlativo_documento as correlativo,
                doc.correlativo_externo as formato,
                doc.descripcion as descripcion
            FROM with_copies cop
                INNER JOIN documentos doc
                    ON cop.document_id = doc.id 
            WHERE user_id = :id",['id' => $idUsuario->original]);

            return response()->json($query,200);
        }

    public function getRecepcion(){


        $usuario = $this->getdepartamentobyId();
        $usuario = json_decode(json_encode($usuario));

        $idUsuario = $this->getUserbyId();
        $idUsuario = json_decode(json_encode($idUsuario));

        

        $trasladoU = traslados::where('idUsuarioTramito',$idUsuario->original)->select('id')->count();


        if($trasladoU > 0){
            
            $trasladoUs = traslados::where('idUsuarioTramito',$idUsuario->original)->select('id')->get();
            $idTranfer = $trasladoUs[0]->id;
            $documento = DB::select("SELECT
            d.id as code,
            d.interesado AS empresa,
            d.correlativo_documento AS correlativo,
            d.descripcion as descripcion,
            ed.id AS estado,
            us.NAME AS usuario,
            DATE_FORMAT(d.created_at, '%d/%m/%Y') as fecha,
            tras.id as idTraslado,
            rol.idRoles as rol,
            d.correlativo_externo as formato,
            d.tracing,
            (SELECT id FROM tracings WHERE idDocumento = d.id AND estado in (4,5)) AS idTracing,
            (SELECT TIMESTAMPDIFF(DAY, NOW(), fechaFinal) FROM tracings WHERE idDocumento = d.id AND estado in (4,5)) AS dias,
            (SELECT 
			(CASE 
				WHEN idUsuarioTraslada = :local THEN 'true'
				ELSE 'false'
				END) AS flag 
                FROM tracings WHERE idDocumento = d.id AND estado in (4,5)) AS flag,
            concat('./../files/',files.`file`) AS url
            FROM documentos d
            INNER JOIN traslados tras
                ON d.id = tras.id
            INNER JOIN estado_documentos ed
            ON tras.estado = ed.id
            INNER JOIN users us
                ON tras.idUsuarioTramito = us.id
            INNER JOIN user_has_roles rol
                ON us.id = rol.idUser
            INNER JOIN upload_files files
   		        ON files.evento_id = d.id
            WHERE us.id = :id  AND d.id_status != 7 AND files.formato = 'pdf'
            ",['id' => $idUsuario->original, 'local' =>$idUsuario->original]);

            

            return response()->json($documento,200);
        }
                         // $mensaje = estado::select(
                         //     'estado.id as code',
                         //     'documentos.dirigido',
                         //     'documentos.direccion',
                         //     'documentos.correlativo_documento',
                         //     'documentos.created_at',
                         //     'estado.estado',
                         //     'traslados.id as traslado',
                         //     'documentos.id as documento')
                         // ->join('traslados','estado.idTraslado','=','traslados.id')
                         // ->join('documentos','traslados.idDocumento','=','documentos.id')
                         // ->where('traslados.id',$idTranfer)
                         // ->where('traslados.estado',6)
                         // ->where('estado.estado','I')
        // }else{
        //     $documento = DB::select('SELECT
        //                                 d.id as code,
        //                                 d.interesado AS empresa,
        //                                 d.correlativo_documento AS correlativo,
        //                                 d.descripcion as descripcion,
        //                                 ed.id AS estado,
        //                                 us.NAME AS usuario,
        //                                 d.created_at as fecha,
        //                                 tras.id as idTraslado
        //                                 FROM documentos d
        //                                 INNER JOIN traslados tras
        //                                     ON d.id = tras.id
        //                                 INNER JOIN estado_documentos ed
        //                                 ON tras.estado = ed.id
        //                                 INNER JOIN users us
        //                                     ON tras.idUsuarioTramito = us.id
        //                                 WHERE us.id = :id
        //     ',['id' => $idUsuario->original]);
        //     // $mensaje = estado::select('estado.id as code','documentos.dirigido','documentos.direccion','documentos.correlativo_documento','documentos.created_at','estado.estado','traslados.id as traslado','documentos.id as documento')
        //     // ->join('traslados','estado.idTraslado','=','traslados.id')
        //     // ->join('documentos','traslados.idDocumento','=','documentos.id')
        //     // ->where('idDepartamento',$usuario->original)
        //     // ->where('traslados.estado',2)
        //     // // ->where('estado.estado','I')
        //     // ->get();
        //     return response()->json($documento,200);
        // }

        // $data = estado::where('idDepartamento',$usuario->original)->where('estado','I')->select('id')->count();
    }

    public function getSeguimientoDocumento(Request $request){
        try {
            DB::beginTransaction();

            $seguimiento = tracing::where(['idDocumento' => $request->code, 'estado' => 4])
            ->select('tracings.instruccion','tracings.instruccion_ministro','tracings.fechaInicial','tracings.fechaFinal','viceministerios.descripcion as viceministerio')
            ->join('viceministerios','tracings.id_vice','=','viceministerios.id')->get();

            DB::commit();

            return response()->json($seguimiento,200);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th,
                'status'    => false
            ],200);
        }
    }

    public function makeBoleta(Request $request){
        $path_img = public_path() . $request->img;
        $fecha_actual = date("d") . "/" . date("m") . "/" . date("Y");
        $html = '
        <style>     
            
                       

            .constancia{
                width: 100% !important;
                border: 1px solid #000;
            }
            
            .constancia-img {
                width: 100% !important;
                vertical-align: top !important;
            }

            // .constancia > table{

            //     height: 600px !important;
            // }

            .constancia > table td{
                height: 30px;
                width: 20% !important;
                vertical-align: top !important;
               
            }

            .titulo{
                font-weight: bolder !important;
                font-size:0.9rem !important;
                text-align: right;
                padding-right: 10px;
            }

            .titulo_1{
                vertical-align: top !important;
                font-size: 1.3rem;
            }

            .subTitulo{
                font-size:0.9rem !important;
            }

        </style>
        <div class="constancia" >
        <table >
          <tbody>
            <tr>
              <td>
                <img src="'.$path_img.'"  height="90px" width="150px">
              </td>
              <td colspan="3" class="titulo_1">
                DELEGACIÓN DE CORRESPONDENCIA DESPACHO SUPERIOR
              </td>
            </tr>
            <tr>
              <td class="titulo">No. Documento:</td>
              <td class="subTitulo">'.$request->correlativo.'</td>
              <td class="titulo">Correlativo Mineco:</td>
              <td class="subTitulo">'.$request->mineco.'</td>
            </tr>
            <tr>
              <td class="titulo">Fecha de Recepción:</td>
              <td >'.$request->fecha.'</td>
              <td class="titulo">Fecha de Impresión:</td>
              <td>'.$fecha_actual.'</td>
            </tr>
            <tr>
              <td class="titulo">Delegado:</td>
              <td colspan="3">
                '.$request->viceministerio.'
              </td>
            </tr>
            <tr>
              <td class="titulo">Cc:</td>
              <td colspan="3">

              </td>
            </tr>
            <tr>
              <td class="titulo">Fecha de entrega a Despacho:</td>
              <td>'.$request->fechaFin.'</td>
              <td class="titulo">Fecha Limite Envío:</td>
              <td>'.$request->fechaFin.'</td>
            </tr>
            <tr>
              <td class="titulo">Remitente Organizacional:</td>
              <td>'.$request->empresa.'</td>
              <td class="titulo">No. Oficio:</td>
              <td>'.$request->correlativo.'</td>
            </tr>
            <tr>
              <td class="titulo">Tema:</td>
              <td colspan="3">
              '.$request->descripcion.'
              </td>
            </tr>
            <tr>
              <td class="titulo">Instruccion Ministro:</td>
              <td colspan="3">
              '.$request->ministro.'
              </td>
            </tr>
            <tr>
              <td class="titulo">Seguimiento:</td>
              <td colspan="3">
              '.$request->general.'
              </td>
            </tr>
          </tbody>
        </table>
        
      </div>';


        $pdf = PDF::loadHTML($html);
        $pdf->setPaper('a4', 'postrait');
        return $pdf->download('prueba.pdf');

    }



    public function getUrlDocument(Request $request){
        $url = uploadFile::select('file')->where(['evento_id' => $request->id, 'formato' => $request->formato])->get();
        return response()->json($url,200);
    }

    public function existDocument(Request $request){
        if(uploadFile::where(['evento_id' => $request->id, 'formato' => $request->formato])->exists()){
            $url = uploadFile::select('file')->where(['evento_id' => $request->id, 'formato' => $request->formato])->get();
            return response()->json($url,200);
        }else{
            return response()->json(false,200);
        }
    }

    public function getRecepcionMessage(){
        $usuario = $this->getdepartamentobyId();
        $usuario = json_decode(json_encode($usuario));

        $idUsuario = $this->getUserbyId();
        $idUsuario = json_decode(json_encode($idUsuario));

        $trasladoU = traslados::where('idUsuarioInterno',$idUsuario->original)->select('id')->count();

        if($trasladoU > 0){
            $trasladoUs = traslados::where('idUsuarioInterno',$idUsuario->original)->select('id')->get();
            $idTranfer = $trasladoUs[0]->id;
            $mensaje = estado::select('estado.id as code','documentos.dirigido','documentos.direccion','documentos.correlativo_documento','documentos.created_at')
            ->join('traslados','estado.idTraslado','=','traslados.id')
            ->join('documentos','traslados.idDocumento','=','documentos.id')
            ->where('traslados.id',$idTranfer)
            ->get();
            return response()->json($mensaje,200);
        }else{
            $mensaje = estado::select('estado.id as code','documentos.dirigido','documentos.direccion','documentos.correlativo_documento','documentos.created_at')
            ->join('traslados','estado.idTraslado','=','traslados.id')
            ->join('documentos','traslados.idDocumento','=','documentos.id')
            ->where('idDepartamento',$usuario->original)
            ->where('estado.estado',5)
            ->get();
            return response()->json($mensaje,200);
        }


    }


    public function toAccept(Request $request){

        try {
            DB::beginTransaction();

            $usuarioId = $this->getUserbyId();
            $usuarioId = json_decode(json_encode($usuarioId));
            $idTraslado = traslados::where('id',$request->code)->select('estado','idUsuarioTramito as Usuario')->get();

            $traslados = new estado;
            $traslados->idTraslado = $request->code;
            $traslados->estadoAnterior = $idTraslado[0]->estado;
            $traslados->estadoActual = 3;
            $traslados->estatus = 4;
            $traslados->UsuarioActual = $idTraslado[0]->Usuario;
            $traslados->save();


            $update = traslados::where('id',$request->code)->update(['estado' => 3]);
            $document = documento::where('documentos.id',$request->code)
                ->join('type_documents','type_documents.id','=','documentos.idTipoDocumento')
                ->select('documentos.correlativo_documento as interno','documentos.correlativo_externo as externo','type_documents.descripcion as tipo')
                ->get();


            $usuarioTo = User::where('id',$usuarioId->original)->select('name','email')->get();
            $usuario = User::where('id',$idTraslado[0]->Usuario)->select('name')->get();
            // send Mail
//            $to_email = $usuarioTo[0]->email;
            // $to_email = "jjolon@mineco.gob.gt";
            // $to_usuario = 'Juan José Jolón';
            $to_usuario = $usuarioTo[0]->name;
            $to_email = $usuarioTo[0]->email;
            $to_internalCorrelative = $document[0]->interno;
            $to_externalCorrelative = $document[0]->externo;
            $to_typeDocument = $document[0]->tipo;
            $to_receivingUser = $usuario[0]->name;


            $subject = 'Información de Documento';
            // Mail::to($to_email)->send(new receiptOfNotification(
            //     $to_usuario,
            //     $to_internalCorrelative,
            //     $to_externalCorrelative,
            //     $to_receivingUser,
            //     $to_typeDocument
            //     ), function ($message){
            //     $message->from('jjolon@mineco.gob.gt','envio');
            // });


            DB::commit();
            return response()->json($update,200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th,200);
        }
    }

    public function getUsersTransfer(){
        $user = User::all();
        return response()->json($user,200);
    }


    public function showBitacora(){
        $permiso = $this->getPermissionById(4);
        if($permiso->original[0]['admin']){
            return view('administrativo.bitacora');
        }elseif($permiso->original[0]['permit']){
            return view('administrativo.bitacora');
        }
        else{
            // return view('admin.home');
            return header( "refresh:0.1;url=/" );
        }
        
    }

    public function bitacoraDocument(Request $request){

        try {
            DB::beginTransaction();
                $documento = DB::select("SELECT tr.id AS CODE, estados.descripcion AS estado, us.NAME AS usuario ,dep.descripcion as dependencia, date_format(es.created_at,'%d/%m/%Y') as fecha FROM estado es
                        INNER JOIN traslados tr
                            ON es.idTraslado = tr.id
                        INNER JOIN documentos doc
                            ON tr.idDocumento = doc.id
                        INNER JOIN estado_documentos estados
                            ON estados.id = es.estadoActual
                        INNER JOIN users us
                            ON es.UsuarioActual = us.id
                        INNER JOIN dependencias dep
                            ON us.id_unidad = dep.id_dependencia
                    WHERE doc.correlativo_documento = :id",["id" => $request->id]);


                return response()->json($documento,200);

                DB::commit();
            } catch (\Throwable $th) {
                return response()->json(false,200);
                DB::rollBack();
        }

    }

    public function previewPDF(){

        return view('administrativo.previewPDF');
    }

    public function getProfesiones(){
        try {
            DB::beginTransaction();
            $data = profesiones::all();
            DB::commit();

            return response()->json($data,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);
        }
    }

    public function getDataPDF(Request $request){
        // try {
        //     DB::beginTransaction();
            date_default_timezone_set('America/Guatemala');
            $data = documento::select('documentos.correlativo_documento as correlativo','documentos.dirigido',
            DB::raw('CONCAT("Guatemala ",DATE_FORMAT(documentos.created_at,"%d"), " de ",
                        CASE DATE_FORMAT(documentos.created_at,"%m")
                            WHEN 1 THEN "Enero"
                            WHEN 2 THEN "Febrero"
                            WHEN 3 THEN "Marzo"
                            WHEN 4 THEN "Abril"
                            WHEN 5 THEN "Mayo"
                            WHEN 6 THEN "Junio"
                            WHEN 7 THEN "Julio"
                            WHEN 8 THEN "Agosto"
                            WHEN 9 THEN "Septiembre"
                            WHEN 10 THEN "Octubre"
                            WHEN 11 THEN "Noviembre"
                            WHEN 12 THEN "Diciembre"
                        END
                        ,
                        " del ", DATE_FORMAT(documentos.created_at,"%Y")) as fecha'),
            'profesiones.descripcion as profesion','documentos.direccion as direccion','documentos.descripcion as texto')
            ->join('profesiones','documentos.idProfesion','=','profesiones.id')
            ->where('documentos.id',$request->code)->get();
            // DB::commit();

            $nameDocument = 'pdf/'. $data[0]->correlativo.'.pdf';
            $fecha = $data[0]->fecha;
            $hoy = getdate();
            // $fecha = "Guatemala, $hoy[mday] de $mes de $hoy[year] ";

            $html = '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Documento</title>

                <style>

                    @page {
                        margin: 0cm 0cm;
                    }
                    body {
                        margin-top: 3cm;
                        margin-left: 2cm;
                        margin-right: 2cm;
                        margin-bottom: 2cm;
                    }

                    header {
                        position: fixed;
                        top: 0cm;
                        left: 2cm;
                        right: 0cm;
                        height: 2cm;

                        /** Extra personal styles **/

                        color: white;
                        text-align: justify;
                        line-height: 1.5cm;

                    }

                    footer {
                        position: fixed;
                        bottom: 0cm;
                        left: 0cm;
                        right: 0cm;
                        height: 2.2cm;

                        /** Extra personal styles **/
                        color: white;
                        text-align: center;
                        line-height: 1.5cm;

                    }
                    footer img, header img {
                        opacity: 0.5;
                    }
                    .fecha , .Correlativo{
                        text-align: right;
                        padding-right: 20% !important;
                        font-weight: bold;
                    }


                    .page-break {
                        page-break-after: always;

                    }

                    .table{
                        width:100%;

                    }

                    .justificado{
                        text-align: justify;
                    }
                </style>
                </head>
                <body>
                <header>
                    <img src="'.public_path('pdf/img/mineco.png').'" />
                </header>
                <footer>
                <img src="'.public_path('pdf/img/footer.jpg').'" width="100%"  height="100%"/>
                </footer>
                <main>
                    <br>


                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="fecha">'.$data[0]->correlativo.'</td>
                            </tr>
                            <tr>
                                <td class="fecha">'.$fecha.'</td>
                            </tr>
                        </tbody>
                    </table>
                    <p>'
                    .$data[0]->profesion.'<br>'
                    .$data[0]->dirigido.'<br>'
                    .$data[0]->direccion.'<br>
                    Ministerio de Economia
                    </p>

                    <span class="justificado">
                        '.$data[0]->texto.'
                    </span>

                    <br>
                    <br>

                    Atentamente.
                </main>

                </body>
                </html>


            ';

            $pdf = \PDF::loadHTML($html);
            $pdf->setPaper('letter','portrait');
            $pdf->save($nameDocument);

            return response()->json($nameDocument,200);
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     return response()->json(false,200);
        // }

    }

    public function getComentario(Request $request){

        // dd($request);
        try {
            DB::beginTransaction();
            $usuario = $this->getUserbyId();
            $usuario = json_decode(json_encode($usuario));
            $data = comentarios::select('comentarios.id as code','users.name as usuario','comentarios.comentario as comentario','comentarios.created_at as fecha')
            ->join('users','comentarios.idUsuario','=','users.id')
            ->join('traslados','traslados.id','=','comentarios.idTraslado')
            ->where(['comentarios.idTraslado' => $request->code, 'comentarios.iddocumento' => $request->documento ])
            ->orderBy('comentarios.created_at','desc')
            // ->where('comentarios.iddocumento','=',$request->documento)
            ->get();

            DB::commit();

            return response()->json($data,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);
        }
    }

    public function getComentarioCopies(Request $request){

        // dd($request);
        try {
            DB::beginTransaction();
            $usuario = $this->getUserbyId();
            $usuario = json_decode(json_encode($usuario));
            $data = comentarios::select('comentarios.id as code','users.name as usuario','comentarios.comentario as comentario','comentarios.created_at as fecha')
            ->join('users','comentarios.idUsuario','=','users.id')
            // ->join('traslados','traslados.id','=','comentarios.idTraslado')
            ->where(['comentarios.iddocumento' => $request->documento ])
            ->orderBy('comentarios.created_at','desc')
            ->get();

            DB::commit();

            return response()->json($data,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);
        }
    }

    public function getPdfFiles(Request $request){
        try {
            DB::beginTransaction();
            $files = uploadFile::select('file')->where(['evento_id' => $request->document, 'estatus' => 4])
                    ->get();
            DB::commit();

            return response()->json($files,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);
        }
    }

    public function setComentario(Request $request){


        try {
            DB::beginTransaction();

            $usuario = $this->getUserbyId();
            $usuario = json_decode(json_encode($usuario));

            $data = new comentarios;

            $data->idUsuario = $usuario->original;
            $data->iddocumento = $request->code;
            $data->idTraslado = $request->traslado;
            $data->comentario = $request->comentario;
            $data->save();
            DB::commit();

            return response()->json($data,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);

        }
    }

    public function closeDocumento(Request $request){
        // try {
        //     DB::beginTransaction();

            $usuarioId = $this->getUserbyId();
            $usuarioId = json_decode(json_encode($usuarioId));

            $data = new comentarios;

            $data->idUsuario = $usuarioId->original;
            $data->iddocumento = $request->code;
            $data->idTraslado = $request->traslado;
            $data->comentario = $request->comentario;
            $data->save();

            $cierre = documento::where('id',$request->code)->update(['id_status' => 7]);
            $trasladoCierre = traslados::where('id', $request->traslado)->update(['estado' => 7]);
            // $estadoData = estado::where('idTraslado',$request->traslado)->select('estadoActual','UsuarioActual')->get();
            $estadoData = estado::where('idTraslado',$request->traslado)->select('estadoActual','UsuarioActual')->orderBy('id','desc')->first();



            $traslados = new estado;
            $traslados->idTraslado = $request->traslado;
            $traslados->estadoAnterior = $estadoData->estadoActual;
            $traslados->estadoActual = 7;
            $traslados->estatus = 7;
            $traslados->UsuarioActual = $usuarioId->original;
            $traslados->save();

            $document = documento::where('documentos.id',$request->code)
                ->join('type_documents','type_documents.id','=','documentos.idTipoDocumento')
                ->select('documentos.correlativo_documento as interno','documentos.correlativo_externo as externo','type_documents.descripcion as tipo')
                ->get();

            $idTraslado = traslados::where('id',$request->code)->select('estado','idUsuarioTramito as Usuario')->get();
            $usuario = User::where('id',$idTraslado[0]->Usuario)->select('name')->get();
            $usuarioTo = User::where('id',$usuarioId->original)->select('name','email')->get();

            // $to_email = "amorozco@mineco.gob.gt";
            // $to_email = "jjolong@miumg.edu.gt";
            // $to_usuario = 'Alexandra Orozco';
            $to_usuario = $usuarioTo[0]->name;
            $to_email = $usuarioTo[0]->email;
            $to_internalCorrelative = $document[0]->interno;
            $to_externalCorrelative = $document[0]->externo;
            $to_typeDocument = $document[0]->tipo;
            $to_receivingUser = $usuario[0]->name;

            $subject = 'Información de Documento';

            $parametro = setting::where(['commandString' => 'SHOW_EMAIL'])->select('valuesString')->get();
            $flag = $parametro[0]->valuesString;

            if($flag == "true"){
                Mail::to($to_email)->send(new receiptOfNotification(
                    $to_usuario,
                    $to_internalCorrelative,
                    $to_externalCorrelative,
                    $to_receivingUser,
                    $to_typeDocument
                    ), function ($message){
                    $message->from($to_email,'envio');
                });
            }



            // DB::commit();

            return response()->json($data,200);
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     return response()->json(false,200);

        // }
    }

    public function getTypeDocument(){
        try {
            $type = typeDocument::all();

            return response()->json($type,200);

        }catch(\Throwable $th){
            return response()->json(false,200);
        }
    }

    /**
     * application settings function
     * return view 
     */
    public function showSettings(){
        return view('settings.settings');
    }

    public function getSetting(){
        $setting = setting::all();

        return response()->json($setting,200);
    }

    public function postSetting(Request $request){
        try {
            DB::beginTransaction();

            $data = new setting;
            $data->commandString = $request->parametro;
            $data->valuesString = $request->valor;
            $data->save();

            DB::commit();
            return response()->json(true,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);
        }
    }

    public function editParameter(Request $request){
        try {
            DB::beginTransaction();

            $data = setting::where(['id' => $request->id])->update(['valuesString' => $request->parametro]);
            
            DB::commit();
            return response()->json(true,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);
        }
    }

    public function checkNumber(Request $request){
        try {
            DB::beginTransaction();

            $flag = documento::where(['correlativo_documento' => $request->number])->exists();
            DB::commit();
            return response()->json($flag,200);
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }

}
