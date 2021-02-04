<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\traslados;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class modulos extends Controller
{
    public function ingresosShow(){
        return view('modules.index');
    }

    public function delegadoShow(){
        return view('modules.delegados');
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


}
