<?php

use Illuminate\Database\Seeder;
use App\Model\sequences;
use App\Model\estado;

class estadoBien extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new sequences;
        $data->name = "estado_productos";
        $data->value = 1;
        $data->save();
        $id = $data->value;

        $unidad = new estado;
        $unidad->id_estadoP = $id;
        $unidad->descripcion = "BUENO";
        $unidad->save();
    }
}
