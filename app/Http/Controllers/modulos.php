<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\traslados;
use App\Model\tracing;
use App\Model\documento;
use App\Model\mailTracking;
use App\Model\view_users;
use App\Model\user_has_view;
use App\Model\userHasRoles;
use App\Model\remitente;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendTracingMailModel;
use App\Mail\sendMessagePrivate;

class modulos extends Controller
{
    public function __construct(){
        ini_set('max_execution_time', 3500);
    }
    
    public function ingresosShow(){
        $permiso = $this->getPermissionById(5);
        if($permiso->original[0]['admin']){
            return view('modules.index');
        }elseif($permiso->original[0]['permit']){
            return view('modules.index');
        }else{
            // return view('admin.home');
            return header( "refresh:0.1;url=/" );
        }
        
    }

    public function visualizador(){

        $permiso = $this->getPermissionById(1);
        if($permiso->original[0]['admin']){
            return view('modules.visualizador');
        }elseif($permiso->original[0]['permit']){
            return view('modules.visualizador');
        }else{
            // return view('admin.home');
            return header( "refresh:0.1;url=/" );
        }
    }

    public function backup(){

        $permiso = $this->getPermissionById(1);
        if($permiso->original[0]['admin']){
            return view('modules.backup');
        }elseif($permiso->original[0]['permit']){
            return view('modules.backup');
        }else{
            // return view('admin.home');
            return header( "refresh:0.1;url=/" );
        }
    }


    public function getRemitente(){
        $permiso = $this->getPermissionById(8);
        if($permiso->original[0]['admin']){
            return view('modules.remitente');
        }elseif($permiso->original[0]['permit']){
            return view('modules.remitente');
        }else{
            // return view('admin.home');
            return header( "refresh:0.1;url=/" );
        }
        
    }

    public function getInbox(){
        $permiso = $this->getPermissionById(1);
        if($permiso->original[0]['admin']){
            return view('modules.inbox');
        }elseif($permiso->original[0]['permit']){
            return view('modules.inbox');
        }else{
            // return view('admin.home');
            return header( "refresh:0.1;url=/" );
        }
        
    }

    public function getSeguimiento(){
        $permiso = $this->getPermissionById(6);
        if($permiso->original[0]['admin']){
            return view('modules.seguimiento');
        }elseif($permiso->original[0]['permit']){
            return view('modules.seguimiento');
        }else{
            // return view('admin.home');
            return header( "refresh:0.1;url=/" );
        }
        
    }

    public function showViews(){
        return view('modules.viewsUser');
    }

    public function showPermits(){
        return view('modules.permits');
    }

    public function delegadoShow(){
        return view('modules.delegados');
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

    public function expedientesByUserId(){

        $usuario = $this->getUserbyId();
        $usuario = json_decode(json_encode($usuario));

        $list = DB::select('
        SELECT 
            d.interesado,
            d.correlativo_documento as correlativo,
            d.descripcion,
            d.correlativo_externo,
            (case when d.dateOfAdmission IS NULL then "Sin fecha"
            else date_format(d.dateOfAdmission,"%d/%m/%Y") end)as fecha_documento,
            (case when d.receptionDate IS NULL then "Sin fecha"
            else date_format(d.receptionDate,"%d/%m/%Y")  end)as fecha_recepcion,
            (case when d.created_at IS NULL then "Sin fecha"
            else date_format(d.created_at,"%d/%m/%Y   %h:%i:%S %p") end)as fecha_ingreso,
            (SELECT u.name FROM estado e INNER JOIN users u ON  e.UsuarioActual = u.id WHERE e.idTraslado = t.id  AND e.id = 
            (SELECT MAX(id) FROM estado WHERE idTraslado = t.id) ) AS usuario
        FROM traslados t
        INNER JOIN documentos d
            ON t.idDocumento = d.id
        INNER JOIN estado e
            ON t.id = e.idTraslado
        WHERE e.UsuarioActual = :id AND e.estadoActual = 1
        ORDER BY d.created_at desc
        ',['id' => $usuario->original]);

        return response()->json($list,200);
    }

    public function getUserbyId(){
        $usuario = Auth::user()->id;
        return response()->json($usuario,200);
    }

    public function tracingsFiles(Request $request){

        try {
            DB::beginTransaction();


            $usuario = $this->getUserbyId();
            $usuario = json_decode(json_encode($usuario));
            $date = date("Y/m/d");

            $documento = documento::where(['id' => $request->documento])->update(['tracing' => '1']);
            if($request->tracing > 0){
                $tracingUpdate = tracing::where(['id' => $request->tracing])->update(['estado' => 4,'fechafinal' => $request->fechaF]);
            }else{
    
                $tracing = new tracing;
                $tracing->idDocumento = $request->documento;
                $tracing->idUsuarioTraslada = $usuario->original;
                // $tracing->idUsuarioActual = $request->usuarioActual;
                $tracing->fechaInicial = $date;
                $tracing->fechafinal = $request->fechaF;
                // $tracing->fechafinal = $date;
                $tracing->estado = 4;
                $tracing->save();
            }
            DB::commit();

            return response()->json($tracing,200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);
        }
    }

    public function inactiveTracingFile(Request $request){
        try {
            DB::beginTransaction();

            $documento = documento::where(['id' => $request->documento])->update(['tracing' => '0']);
            $tracing = tracing::where(['id' => $request->tracing])->update(['estado' => 5]);

            DB::commit();

            return response()->json($tracing,200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(false,200);
        }
        
    }

    public function getFollowUp(){
        try {
            DB::beginTransaction();

            $usuario = $this->getUserbyId();
            $usuario = json_decode(json_encode($usuario));
           
            

            $data = DB::select('
            SELECT 
                t.id AS idTracing,
                tr.id as transfer,
                d.id,
                d.interesado AS Remitente,
                d.correlativo_documento AS correlativo,
                d.correlativo_externo AS correlativo_interno,
                t.fechaFinal AS final,
                TIMESTAMPDIFF(DAY, NOW(), t.fechaFinal) AS Dias,
                (
                    CASE 
                        WHEN (TIMESTAMPDIFF(DAY, NOW(), t.fechaFinal)) >= 0 THEN 1
                        WHEN (TIMESTAMPDIFF(DAY, NOW(), t.fechaFinal)) < 0 THEN 0
                    END
                ) AS retardo,
                u.NAME AS nombre_traslada,
                (SELECT MAX(us.name) FROM estado es INNER JOIN users us ON es.UsuarioActual = us.id WHERE es.idTraslado = tr.id AND es.estatus = 4) AS usuarioActual,
                CONCAT("./../files/",files.`file`) AS url
            FROM tracings t
            INNER JOIN documentos d
                ON t.idDocumento = d.id
            INNER JOIN users u
                ON t.idUsuarioTraslada = u.id
            INNER JOIN traslados tr
                ON t.idDocumento = tr.idDocumento
            INNER JOIN upload_files files
		        ON files.evento_id = d.id
            WHERE t.idUsuarioTraslada = :id AND files.formato = "pdf" AND d.id_status != 7
            ORDER BY retardo desc
            ',['id' => $usuario->original]);

            // $encriptado = Crypt::encrypt($data);
            // $desencriptado = Crypt::decrypt($encriptado);

           
            DB::commit();

            return response()->json($data,200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(false,200);
        }
    }

    public function sendTracingMail(Request $request){
        // try {
        //     DB::beginTransaction();

            $send = new mailTracking;
            $send->idTracings = $request->tracing;
            $send->message = $request->message;
            $send->save();
            $data = DB::select('
                SELECT 
                        (
                            CASE
                                WHEN t.idUsuarioActual IS NULL
                                THEN (SELECT email FROM users u WHERE u.id = t.idUsuarioTraslada)
                                ELSE (SELECT email FROM users u WHERE u.id = t.idUsuarioActual)
                                END
                        ) AS email
                FROM tracings t
                WHERE t.id = :id
            ',['id' => $request->tracing]);
            // dd($data[0]->email);
            // $to_email = 'jjolong@miumg.edu.gt';
            $to_email = $data[0]->email;
            $to_message = $request->message;
            $to_traslada =$request->traslada;
            $to_actual = $request->actual;
            $to_correlativo = $request->correlativo;
            Mail::to($to_email)->send(new sendMessagePrivate($to_message,$to_actual,$to_traslada,$to_correlativo));
            // Mail::to($to_email)->send(new SendTracingMailModel($to_message,$to_actual,$to_traslada,$to_correlativo));
            
            // DB::commit();

            return response()->json($send,200);
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     return response()->json(false,200);
        // }
    }

    public function getMessagesTracking(Request $request){
        try {
            DB::beginTransaction();

            $data = DB::select('
            SELECT mt.message, u.NAME, date_format(mt.created_at,"%d/%m/%Y   %h:%i:%S %p") AS fecha
                FROM mail_trackings mt
                INNER JOIN tracings t
                    ON mt.idTracings = t.id
                INNER JOIN users u
                    ON t.idUsuarioTraslada = u.id
            WHERE idTracings = :id
            ',['id' => $request->id]);

            DB::commit();

            return response()->json($data,200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(false,200);
        }
    }


    public function getViewsUsers(){
        $data  = view_users::all();
        return response()->json($data,200);
    }

    public function setViewsUser(Request $request){
        try {
            DB::beginTransaction();

            $data = new view_users;

            $data->description = $request->name;
            $data->idEstado = 4;
            $data->save();

            DB::commit();

            return response()->json($data,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);
        }
    }

    public function getPermisoUsuario(){
        try {
            DB::beginTransaction();

            $permits =  DB::select("
            SELECT 
                us.description,
                vu.description AS permiso
            FROM user_has_views uv
                INNER JOIN view_users vu
                    ON uv.permits = vu.id
                INNER JOIN roles_users us
                    ON uv.rol = us.id;
        
            ");

            DB::commit();

            return response()->json($permits,200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(false,200);
        }
    }

    public function setPermiso(Request $request){
        try {
            DB::beginTransaction();

            $cantidad = sizeof($request->view);

            
            for ($item=0; $item < $cantidad ; $item++) { 
                $data = new user_has_view;
                $data->rol = $request->user;
                $data->permits = $request->view[$item];
                $data->estado = 4;
                $data->save();
                
            }

            DB::commit();

            return response()->json($data,200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(false,200);
        }
    }

    public function remitente(){
        $data = remitente::where(['estatus' => 4])->selectRaw('id, UPPER(descripcion) as descripcion')->get();

        return response()->json($data,200);
    }

    public function setSender(Request $request){
        try {
            DB::beginTransaction();

            $sender = new remitente;

            $sender->descripcion = $request->sender;
            $sender->save();

            DB::commit();
            return response()->json($sender,200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json($th,100);
        }
    }

    public function getSender(){
        try {
            DB::beginTransaction();

            $dSender = remitente::where(['estatus' => 4])->selectRaw('id,UPPER(descripcion) as descripcion')->orderBy('id','desc')->get();

            DB::commit();

            return response()->json($dSender,200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(false,200);
        }
    }

    public function deleteSender(Request $request){
        try {
            DB::beginTransaction();

            $dSender = remitente::where(['id' => $request->id])->update(['estatus' => 5]);

            DB::commit();

            return response()->json($dSender,200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(false,200);
        }
    }

    public function email(){
        return view('emails.send-private');
    }

    public function getListUpload(){
        $upload = DB::select("
        SELECT 
            u.id CODE_UPLOAD, 
            u.file NOMBRE_ARCHIVO, 
            u.file_name NOMBRE, 
            u.formato AS FORMATO, 
            d.interesado REMITENTE, 
            d.correlativo_documento CORRELATIVO, 
            d.correlativo_externo CORRELATIVO_MINECO,
            CONCAT('./files/',u.file) URL,
            dep.id_dependencia CODE_DIRECCION,
            dep.descripcion DIRECCION,
            vices.id CODE_VICEMINISTERIO,
            vices.descripcion VICEMINISTERIO
        FROM upload_files u
            INNER JOIN 	documentos d
                ON u.evento_id = d.id
            INNER JOIN traslados tr
                ON tr.idDocumento = d.id
            INNER JOIN users us
                ON tr.idUsuarioTramito = us.id
            INNER JOIN dependencias dep
                ON us.id_unidad = dep.id_dependencia
            INNER JOIN viceministerios vices
                ON dep.idVice = vices.id
            WHERE u.formato = 'pdf'
                ORDER BY u.id desc
        limit 10;
        ");

        return response()->json($upload,200);
    }

    public function getFileByFilter(Request $request){
        $where = '';

        if($request->multiple){
            if(count($request->direction) > 0){
                $where .= ' and dep.id_dependencia IN(' . implode(',',$request->direction) . ')';
            }
            
            if(count($request->vice) > 0){
                $where .= ' and vices.id IN(' . implode(',',$request->vice) . ')';
            }
            
            if(!is_null($request->internal)){
                $where .= ' and d.correlativo_externo like "%' . $request->internal . '%"';
            }
        }else{
            if(!is_null($request->direction)){
                $where .= ' and dep.id_dependencia IN(' . $request->direction. ')';
            }
            
            if(!is_null($request->vice)){
                $where .= ' and vices.id IN(' . $request->vice . ')';
            }
            
            if(!is_null($request->internal)){
                $where .= ' and d.correlativo_externo like "%' . $request->internal . '%"';
            }
        }


        $where .= ' ORDER BY u.id desc';
       

        try {
            DB::beginTransaction();

            $sql = DB::select("
            SELECT 
                u.id CODE_UPLOAD, 
                u.file NOMBRE_ARCHIVO, 
                u.file_name NOMBRE, 
                u.formato  FORMATO, 
                d.interesado REMITENTE, 
                d.correlativo_documento CORRELATIVO, 
                d.correlativo_externo CORRELATIVO_MINECO,
                CONCAT('./files/',u.file) URL,
                dep.id_dependencia CODE_DIRECCION,
                dep.descripcion DIRECCION,
                vices.id CODE_VICEMINISTERIO,
                vices.descripcion VICEMINISTERIO
            FROM upload_files u
                    INNER JOIN 	documentos d
                    ON u.evento_id = d.id
                    INNER JOIN traslados tr
                    ON tr.idDocumento = d.id
                    INNER JOIN users us
                    ON tr.idUsuarioTramito = us.id
                    INNER JOIN dependencias dep
                    ON us.id_unidad = dep.id_dependencia
                    INNER JOIN viceministerios vices
                    ON dep.idVice = vices.id
                WHERE u.formato = 'pdf'". $where);


            DB::commit();

            return response()->json($sql,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);
        }
    }

    public function getViceministerio(){
        $vice = DB::select('SELECT * FROM viceministerios');

        return response()->json($vice,200);
    }



}
