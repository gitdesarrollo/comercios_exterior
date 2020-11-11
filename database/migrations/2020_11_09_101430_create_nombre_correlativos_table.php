<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNombreCorrelativosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nombre_correlativos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',200);
            $table->bigInteger('unidad_id')->unsigned();
            $table->timestamps();

            $table->foreign('unidad_id')->references('id_dependencia')->on('dependencias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nombre_correlativos');
    }
}
