<?php

use Illuminate\Database\Seeder;
use App\Model\sequences;
use App\Model\entidad;

class EntidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new sequences;
        $data->name = "entidads";
        $data->value = 1;
        $data->save();
        $id = $data->value;

        $entidad = new entidad;
        $entidad->id_entidad = $id;
        $entidad->name = "Ministerio de Economía";
        $entidad->save();
        
    }
}
