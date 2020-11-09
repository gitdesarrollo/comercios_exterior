<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrasladoExternosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traslado_externos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('traslado_id')->unsigned();
            $table->string('lugar_destino',500);
            $table->timestamps();


            $table->foreign('traslado_id')->references('id')->on('traslados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traslado_externos');
    }
}
