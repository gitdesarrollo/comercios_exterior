<?php

use Illuminate\Database\Seeder;
use App\Model\estado_documento;

class estadoDocumento extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


    $estado = new estado_documento;
    $estado->descripcion = 'creado';
    $estado->save();

    $estado = new estado_documento;
    $estado->descripcion = 'traslado';
    $estado->save();

    $estado = new estado_documento;
    $estado->descripcion = 'recibido';
    $estado->save();

    $estado = new estado_documento;
    $estado->descripcion = 'activo';
    $estado->save();

    $estado = new estado_documento;
    $estado->descripcion = 'inactivo';
    $estado->save();

    $estado = new estado_documento;
    $estado->descripcion = 'traslado Interno';
    $estado->save();

    $estado = new estado_documento;
    $estado->descripcion = 'archivado';
    $estado->save();

    $estado = new estado_documento;
    $estado->descripcion = 'pendiente';
    $estado->save();

    $estado = new estado_documento;
    $estado->descripcion = 'Traslado Externo';
    $estado->save();


        // DB::table('estado_documentos')->insert([
        //     'descripcion' => 'creado'
        // ]); 
        // DB::table('estado_documentos')->insert([
        //     'descripcion' => 'traslado'
        // ]); 
        // DB::table('estado_documentos')->insert([
        //     'descripcion' => 'recibido'
        // ]); 
        // DB::table('estado_documentos')->insert([
        //     'descripcion' => 'activo'
        // ]); 
        // DB::table('estado_documentos')->insert([
        //     'descripcion' => 'inactivo'
        // ]); 
        // DB::table('estado_documentos')->insert([
        //     'descripcion' => 'trasladoInterno'
        // ]); 
        // DB::table('estado_documentos')->insert([
        //     'descripcion' => 'archivado'
        // ]); 
    } 
}
