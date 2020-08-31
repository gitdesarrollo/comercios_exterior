<?php

use Illuminate\Database\Seeder;
use App\Model\sequences;
use App\Model\biene;

class bienes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new sequences;
        $data->name = "bienes";
        $data->value = 1;
        $data->save();
        $id = $data->value;

        $unidad = new biene;
        $unidad->id_bien = $id;
        $unidad->id_tipo = 1;
        $unidad->name = "INVENTARIO INICIAL";
        $unidad->save();
    }
}
