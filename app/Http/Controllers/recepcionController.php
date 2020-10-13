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
use Illuminate\Support\Facades\Mail;

class recepcionController extends Controller
{
    public function recepcion(){
        return view('administrativo.recepcion');
    }

    public function getUserbyId(){
        $usuario = Auth::user()->id;
        return response()->json($usuario,200);
    }

    public function storeRecepcion(Request $request){ 

      
        try {
            DB::beginTransaction();
            $usuario = $this->getUserbyId();
            $usuario = json_decode(json_encode($usuario));
            
            $documento = new documento; 

            $documento->interesado = $request->interesado;
            $documento->correlativo_documento = $request->correlativo;
            $documento->folios = $request->folio;
            $documento->descripcion = $request->descripcion;
            $documento->id_status = 1;
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


            $usuarioTo = User::where('id',$request->usuario)->select('name','email')->get();

            $to_name = $usuarioTo[0]->name;
            $to_email = 'jjolong@miumg.edu.gt';
            $to_empresa = $request->interesado;
            $to_numero = $request->correlativo;
            $to_asunto = $request->descripcion;
            $data = array('name'=> $usuarioTo[0]->name);
            $subject = 'Recepción de Documento';

            Mail::to($to_email)->send(new NotificationMail($to_name,$to_empresa,$to_numero,$to_asunto,$subject), function ($message){
                
                $message->from('jjolon@correo.com','envio');
            });
            
            DB::commit();

            return response()->json($documento,200);
        } catch (\Throwable $th) {
            return response()->json(false,200);
            DB::rollBack();
        }
    }
}
