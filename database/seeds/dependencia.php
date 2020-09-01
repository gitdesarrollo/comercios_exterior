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
        $unidad = new dependencias;
        $unidad->id_dependencia = 1;
        $unidad->descripcion = "Comercio Exterior";
        $unidad->save();
    }
}
