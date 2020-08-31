<?php

use Illuminate\Database\Seeder;
use App\Model\sequences;
use App\Model\tipo;

class tipos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new sequences;
        $data->name = "tipos";
        $data->value = 1;
        $data->save();
        $id = $data->value;

        $unidad = new tipo;
        $unidad->id_tipo = $id;
        $unidad->id_seccion = 1;
        $unidad->name = "MUEBLES DE METAL ESCRITORIO SILLAS";
        $unidad->save();
    }
}
