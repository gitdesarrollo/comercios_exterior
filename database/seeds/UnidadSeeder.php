<?php

use Illuminate\Database\Seeder;
use App\Model\unidaEjecutora;
use App\Model\sequences;

class UnidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new sequences;
        $data->name = "unida_ejecutoras";
        $data->value = 1;
        $data->save();
        $id = $data->value;

        $unidad = new unidaEjecutora;
        $unidad->id_unidad = $id;
        $unidad->id_entidad = 1;
        $unidad->name = "Ministerio de Economía"; 
        $unidad->save();
    }
}
