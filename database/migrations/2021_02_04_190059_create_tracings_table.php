<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idDocumento')->unsigned();
            $table->bigInteger('idUsuarioTraslada')->unsigned();
            $table->bigInteger('idUsuarioActual')->unsigned()->nullable();
            $table->date('fechaInicial')->nullable();
            $table->date('fechaFinal')->nullable();
            $table->bigInteger('estado')->unsigned();
            $table->timestamps();

            $table->foreign('idDocumento')->references('id')->on('documentos');
            $table->foreign('idUsuarioTraslada')->references('id')->on('users');
            $table->foreign('idUsuarioActual')->references('id')->on('users');
            $table->foreign('estado')->references('id')->on('estado_documentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracings');
    }
}
