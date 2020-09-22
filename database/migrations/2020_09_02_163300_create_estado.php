<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idTraslado')->unsigned();
            $table->bigInteger('estadoAnterior')->unsigned();
            $table->bigInteger('estadoActual')->unsigned();
            $table->bigInteger('estatus')->unsigned();
            $table->bigInteger('UsuarioActual')->unsigned();
            $table->timestamps();

            $table->foreign('idTraslado')->references('id')->on('traslados');
            $table->foreign('estadoAnterior')->references('id')->on('estado_documentos');
            $table->foreign('estadoActual')->references('id')->on('estado_documentos');
            $table->foreign('estatus')->references('id')->on('estado_documentos');
            $table->foreign('UsuarioActual')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estado');
    }
}
