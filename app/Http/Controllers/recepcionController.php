<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\documento;
use App\Model\traslados;
use App\Model\estado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        // dd($request);
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


            DB::commit();

            return response()->json($traslado,200);
        } catch (\Throwable $th) {
            return response()->json(false,200);
            DB::rollBack();
        }
    }
}
