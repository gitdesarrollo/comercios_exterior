<?php

use Illuminate\Database\Seeder;
use App\Model\persona;
use App\Model\sequences;

class PersonDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new sequences;
        $data->name = "personas";
        $data->value = 1;
        $data->save();
        $id = $data->value;

        $unidad = new persona;
        $unidad->id_persona = $id;
        $unidad->nombres = "Inventario";
        $unidad->apellidos = "Inicial";
        $unidad->cargo = "0";
        $unidad->id_dependencia = 1;
        $unidad->id_entidad = 1;
        $unidad->nit = "0";
        $unidad->nivel = 1;
        $unidad->extension = 0;
        $unidad->activo = "A";
        $unidad->save();
    }
}
