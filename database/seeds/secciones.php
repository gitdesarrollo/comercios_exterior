<?php

use Illuminate\Database\Seeder;
use App\Model\sequences;
use App\Model\seccion;

class secciones extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new sequences;
        $data->name = "seccions";
        $data->value = 1;
        $data->save();
        $id = $data->value;

        $unidad = new seccion;
        $unidad->id_seccion = $id;
        $unidad->id_categoria = 1;
        $unidad->name = "EQUIPO DE OFICINA";
        $unidad->save();
    }
}
