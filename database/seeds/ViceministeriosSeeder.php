<?php

use Illuminate\Database\Seeder;
use App\viceministerio;

class ViceministeriosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $unidad = new viceministerio;
        $unidad->descripcion = "Despacho Superior";
        $unidad->idEstado = 4;
        $unidad->save();
        
        $unidad = new viceministerio;
        $unidad->descripcion = "Viceministerio de Inversión y Competencia";
        $unidad->idEstado = 4;
        $unidad->save();

        $unidad = new viceministerio;
        $unidad->descripcion = "Viceministerio de Asuntos Registrales";
        $unidad->idEstado = 4;
        $unidad->save();

        $unidad = new viceministerio;
        $unidad->descripcion = "Viceministerio de Integración y Comercio Exterior";
        $unidad->idEstado = 4;
        $unidad->save();

        $unidad = new viceministerio;
        $unidad->descripcion = "Viceministerio de Desarrollo de la Microempresa, Pequeña y Mediana Empresa";
        $unidad->idEstado = 4;
        $unidad->save();

        $unidad = new viceministerio;
        $unidad->descripcion = "Viceministerio de Administrativo y Financiero";
        $unidad->idEstado = 4;
        $unidad->save();

    }
}
