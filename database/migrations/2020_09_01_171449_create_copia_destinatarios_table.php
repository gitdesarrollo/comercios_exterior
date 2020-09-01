<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCopiaDestinatariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('copia_destinatarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_copia_dependencia')->unsigned();
            $table->bigInteger('id_receptor')->unsigned();
            $table->bigInteger('id_documento')->unsigned();
            $table->timestamps();

            $table->foreign('id_copia_dependencia')->references('id_dependencia')->on('dependencias');
            $table->foreign('id_receptor')->references('id')->on('receptors');
            $table->foreign('id_documento')->references('id')->on('documentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('copia_destinatarios');
    }
}
