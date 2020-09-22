<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraslados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traslados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idDocumento')->unsigned();
            // $table->bigInteger('idDepartamentoActual')->unsigned();
            // $table->bigInteger('idDepartamentoTraslado')->unsigned();
            $table->bigInteger('idUsuarioTramito')->unsigned();
            $table->bigInteger('estado')->unsigned();
            // $table->bigInteger('idUsuarioInterno')->unsigned()->nullable();
            $table->timestamps();


            $table->foreign('idDocumento')->references('id')->on('documentos');
            // $table->foreign('idDepartamentoActual')->references('id_dependencia')->on('dependencias');
            // $table->foreign('idDepartamentoTraslado')->references('id_dependencia')->on('dependencias');
            $table->foreign('idUsuarioTramito')->references('id')->on('users');
            $table->foreign('estado')->references('id')->on('estado_documentos');
            // $table->foreign('idUsuarioInterno')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traslados');
    }
}
