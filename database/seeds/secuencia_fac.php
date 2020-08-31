<?php

use Illuminate\Database\Seeder;
use App\Model\sequences;
use App\Model\secuencias_factura;

class secuencia_fac extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new sequences;
        $data->name = "secuencias";
        $data->value = 1;
        $data->save();
        $id = $data->value;

        $unidad = new secuencias_factura;
        $unidad->id_secuencia = $id;
        $unidad->name = "FACTURA CONFORME COMPRA DIRECTA";
        $unidad->save();
    }
}
