<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\receptor;
use App\Model\documento;
use App\Model\copiaDestinatarios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class documentos extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function showCreate(){
        $usuario = Auth::user()->id_unidad;
        
        return view('administrativo.showCreate',[
            'departamento'   => $usuario
        ]);
    }

    public function showReceptores(){
        return view('administrativo.showReceptores');
    }

    public function createDocumento(Request $request){

        
        $count_copias = sizeof($request->copia);
        $count_receptor = sizeof($request->receptor);
        
        if($count_copias === $count_receptor){
            try {
                DB::beginTransaction();
    
                $documento = new documento;
                $documento->dirigido = $request->dirigido;
                $documento->id_dependencia = $request->departamento;
                $documento->descripcion = $request->cuerpo;
                $documento->correlativo_documento = $request->correlativo;
                $documento->save();
                $id_documento = $documento->id;
    

                for($iteration = 0; $iteration < $count_copias; $iteration++){

                    $copias = new copiaDestinatarios;
                    $copias->id_copia_dependencia = $request->copia[$iteration];
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
            return response()->json(false,200);
        }
        



    }

    public function getReceptores(){
        $data = receptor::all();
        return response()->json($data,200);
    }

    public function setReceptores(Request $request){
        try {
            DB::beginTransaction();

            $receptor = new receptor;

            $receptor->descripcion = $request->name;
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
}
