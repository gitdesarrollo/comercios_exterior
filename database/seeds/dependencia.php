<?php

use Illuminate\Database\Seeder;
use App\Model\sequences;
use App\Model\dependencias;

class dependencia extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new sequences;
        $data->name = "dependencias";
        $data->value = 1;
        $data->save();
        $id = $data->value;

        $unidad = new dependencias;
        $unidad->id_dependencia = $id;
        $unidad->descripcion = "MINISTERIO DE ECONOMIA";
        $unidad->save();
    }
}
