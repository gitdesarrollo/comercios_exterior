<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViceHasDepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vice_has_deps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('viceministro')->unsigned();
            $table->bigInteger('viceministerios')->unsigned();
            $table->bigInteger('estado')->unsigned();
            $table->timestamps();

            $table->foreign('viceministro')->references('id')->on('users');
            $table->foreign('viceministerios')->references('id')->on('viceministerios');
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
        Schema::dropIfExists('vice_has_deps');
    }
}
