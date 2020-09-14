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
            $table->bigInteger('idDepartamento')->unsigned();
            $table->bigInteger('estado')->unsigned();
            $table->timestamps();

            $table->foreign('idTraslado')->references('id')->on('traslados');
            $table->foreign('idDepartamento')->references('id_dependencia')->on('dependencias');
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
        Schema::dropIfExists('estado');
    }
}
