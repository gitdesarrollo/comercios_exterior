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
        $unidad->descripcion = "Ministerio de Economía";
        $unidad->idEstado = 4;
        $unidad->save();
    }
}
