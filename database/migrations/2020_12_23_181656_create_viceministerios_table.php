<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViceministeriosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viceministerios', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('descripcion');
            $table->bigInteger('idEstado')->unsigned();
            $table->timestamps();       


            $table->foreign('idEstado')->references('id')->on('estado_documentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('viceministerios');
    }
}
