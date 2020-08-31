<?php

use Illuminate\Database\Seeder;
use App\Model\sequences;
use App\Model\categoria;

class categorias extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $data = new sequences;
        // $data->name = "categorias";
        // $data->value = 1;
        // $data->save();
        // $id = $data->value;
        
        $unidad = new categoria;
        $unidad->id_categoria = $id;
        $unidad->id_grupo = 3;
        $unidad->name = "BIENES PREEXISTENTES";
        $unidad->save();
    }
}
