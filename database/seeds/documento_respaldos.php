<?php

use Illuminate\Database\Seeder;
use App\Model\sequences;
use App\Model\documentos_respaldo;

class documento_respaldos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new sequences;
        $data->name = "documento_respaldos";
        $data->value = 1;
        $data->save();
        $id = $data->value;

        $unidad = new documentos_respaldo;
        $unidad->id_documento_respaldo = $id;
        $unidad->name = "COMPROBANTES FISCALES";
        $unidad->save();
    }
}
