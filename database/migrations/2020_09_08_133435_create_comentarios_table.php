<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComentariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idUsuario')->unsigned();;
            $table->bigInteger('iddocumento')->unsigned();;
            $table->bigInteger('idTraslado')->unsigned();;
            $table->string('comentario');
            $table->timestamps();

            $table->foreign('idTraslado')->references('id')->on('traslados');
            $table->foreign('iddocumento')->references('id')->on('documentos');
            $table->foreign('idUsuario')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comentarios');
    }
}
