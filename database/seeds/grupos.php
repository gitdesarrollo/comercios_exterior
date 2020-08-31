<?php

use Illuminate\Database\Seeder;
use App\Model\grupo;
use App\Model\sequences;

class grupos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $data = new sequences;
        // $data->name = "grupos";
        // $data->value = 1;
        // $data->save();
        // $id = $data->value;

        $unidad = new grupo;
        $unidad->id_grupo = 3;
        $unidad->name = "Inventario Inicial";
        $unidad->save();
    }
}
