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


        $unidad = new dependencias;
        $unidad->descripcion = "Integración Económica";
        $unidad->idEstado = 4;
        $unidad->save();

        $unidad = new dependencias;
        $unidad->descripcion = "Negociaciones Comerciales";
        $unidad->idEstado = 4;
        $unidad->save();

        $unidad = new dependencias;
        $unidad->descripcion = "Promoción Comercial";
        $unidad->idEstado = 4;
        $unidad->save();

        $unidad = new dependencias;
        $unidad->descripcion = "Dirección de Comercio Exterior -DACE-";
        $unidad->idEstado = 4;
        $unidad->save();

        $unidad = new dependencias;
        $unidad->descripcion = "Dirección de Análisis Económico -DAE-";
        $unidad->idEstado = 4;
        $unidad->save();

        $unidad = new dependencias;
        $unidad->descripcion = "Dirección de Tecnologías de la Información";
        $unidad->idEstado = 4;
        $unidad->save();
    }
}
