<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\documento;
use App\Model\traslados;
use App\Model\estado;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Mail\NotificationMail;
use App\Mail\NotificationCopies;
use App\Model\correlativos;
use App\Model\nombreCorrelativo;
use App\Model\setting;
use App\Model\user_has_view;
use App\Model\userHasRoles;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Model\withCopy;

class recepcionController extends Controller
{

    public function __construct(){
        ini_set('max_execution_time', 3500);
        $this->middleware('auth');
    }

    public function recepcion(){

        $permiso = $this->getPermissionById(1);
        if($permiso->original[0]['admin']){
            return view('administrativo.recepcion');
        }elseif($permiso->original[0]['permit']){
            return view('administrativo.recepcion');
        }else{
            // return view('admin.home');
            return header( "refresh:0.1;url=/" );
        }
    }


    public function getUserbyId(){
        $usuario = Auth::user()->id;
        return response()->json($usuario,200);
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

    public function getCorrelativoDocumento($unidad){

        $carbon = Carbon::now();

        $anio = $carbon->isoFormat('YYYY');

        if(correlativos::select('numero')->where(['unidad_id' => $unidad, 'ano' => $anio])->count() === 0){

            $string = nombreCorrelativo::select('id')->where(['unidad_id' => $unidad])->get();

            $data = new correlativos;

            $data->unidad_id = $unidad;
            $data->string_id = $string[0]->id;
            $data->numero = 1;
            $data->ano = $anio;
            $data->save();
            

            $formato = correlativos::where(['correlativos.unidad_id' => $unidad, 'correlativos.ano' => $anio])
                ->join('nombre_correlativos','nombre_correlativos.id','=','correlativos.string_id')->selectRaw('correlativos.id, concat(nombre_correlativos.name,correlativos.numero,"-",correlativos.ano) as formato')->get();


            return response()->json($formato,200);
        }else{

            $infoAnterior = correlativos::where(['unidad_id' => $unidad, 'ano' => $anio])->select('numero','id')->get();

            $update = correlativos::where(['id' => $infoAnterior[0]->id])->update(['numero' => ($infoAnterior[0]->numero + 1)]);

            $formato = correlativos::where(['correlativos.unidad_id' => $unidad, 'correlativos.ano' => $anio])
                ->join('nombre_correlativos','nombre_correlativos.id','=','correlativos.string_id')->selectRaw('correlativos.id, concat(nombre_correlativos.name,correlativos.numero,"-",correlativos.ano) as formato')->get();

            return response()->json($formato,200);
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


    public function storeRecepcion(Request $request){


      

        // try {
        //     DB::beginTransaction();
            $usuario = $this->getUserbyId();
            $usuario = json_decode(json_encode($usuario));

            $usuarioTo = User::where('id',$request->usuario)->select('name','email','id_unidad')->get();

            $correlativoFormato = $this->getCorrelativoDocumento($usuarioTo[0]->id_unidad);
            
            $correlativoFormato = json_decode(json_encode($correlativoFormato));

            // dd($correlativoFormato->original[0]->formato);
            // dd($correlativoFormato);

            $documento = new documento;

            $documento->interesado = $request->interesado;
            $documento->correlativo_documento = $request->correlativo;
            $documento->folios = $request->folio;
            $documento->descripcion = $request->descripcion;
            $documento->id_status = 1;
            $documento->idTipoDocumento = $request->type;
            $documento->dateOfAdmission = $request->fechaDocumento;
            $documento->ReceptionDate = $request->fechaRecepcion;
            $documento->correlativo_externo = $correlativoFormato->original[0]->formato;
            $documento->save();
            $id = $documento->id;

            $traslado = new traslados;

            $traslado->idDocumento = $id;
            $traslado->idUsuarioTramito = $request->usuario;
            $traslado->estado = 2;
            $traslado->save();
            $idTraslado = $traslado->id;

            $estado = new estado;

            $estado->idTraslado = $idTraslado;
            $estado->estadoAnterior = 1;
            $estado->estadoActual = 1;
            $estado->estatus = 5;
            $estado->UsuarioActual = $usuario->original;
            $estado->save();

            $estado = new estado;

            $estado->idTraslado = $idTraslado;
            $estado->estadoAnterior = 1;
            $estado->estadoActual = 8;
            $estado->estatus = 4;
            $estado->UsuarioActual = $request->usuario;
            $estado->save();


            foreach($request->copy as $item){
                $copy = new withCopy;
                $copy->document_id = $id;
                $copy->user_id = $item;
                $copy->save();
            }

            $email_copy = withCopy::select('users.email as correo','users.name as user')
                ->join('documentos','with_copies.document_id','=','documentos.id')
                ->join('users','with_copies.user_id','=','users.id')
                ->where(['with_copies.document_id' => $id])
                ->get();

            
            $to_name = $usuarioTo[0]->name;
            // $to_email = 'jjolong@miumg.edu.gt';
            $to_email = $usuarioTo[0]->email;
            // $to_email = 'mahernandez@mineco.gob.gt';
            $to_empresa = $request->interesado;
            $to_numero = $request->correlativo;
            $to_asunto = $request->descripcion;
            $data = array('name'=> $usuarioTo[0]->name);
            $subject = 'Recepción de Documento';
            $correlativo_interno = $correlativoFormato->original[0]->formato;

            $parametro = setting::where(['commandString' => 'SHOW_EMAIL'])->select('valuesString')->get();
            
            $flag = $parametro[0]->valuesString;

            if($flag == "true"){
                Mail::to($to_email)->send(new NotificationMail($to_name,$to_empresa,$to_numero,$to_asunto,$subject,$correlativo_interno), function ($message){
                    $message->from($to_email,'envio');
                });
            }


            
            foreach($email_copy as $correo){
                
                if($flag == "true"){
                    Mail::to($correo->correo)->send(new NotificationCopies($correo->user,$to_empresa,$to_numero,$to_asunto,$subject,$correlativo_interno), function ($message){
                        $message->from($correo->correo,'envio');
                    });
                }
            }



            // DB::commit();

            return response()->json($documento,200);
        // } catch (\Throwable $th) {
        //     return response()->json(false,200);
        //     DB::rollBack();
        // }
    }
}
