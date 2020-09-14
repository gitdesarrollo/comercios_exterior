<?php

use Illuminate\Database\Seeder;

class estadoDocumento extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('estado_documentos')->insert([
            'descripcion' => 'creado'
        ]); 
        DB::table('estado_documentos')->insert([
            'descripcion' => 'traslado'
        ]); 
        DB::table('estado_documentos')->insert([
            'descripcion' => 'recibido'
        ]); 
        DB::table('estado_documentos')->insert([
            'descripcion' => 'activo'
        ]); 
        DB::table('estado_documentos')->insert([
            'descripcion' => 'inactivo'
        ]); 
        DB::table('estado_documentos')->insert([
            'descripcion' => 'trasladoInterno'
        ]); 
    } 
}
