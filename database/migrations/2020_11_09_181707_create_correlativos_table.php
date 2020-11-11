<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorrelativosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correlativos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('unidad_id')->unsigned();
            $table->bigInteger('string_id')->unsigned();
            $table->bigInteger('numero');
            $table->string('ano');
            $table->timestamps();

            $table->foreign('unidad_id')->references('id_dependencia')->on('dependencias');
            $table->foreign('string_id')->references('id')->on('nombre_correlativos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('correlativos');
    }
}
