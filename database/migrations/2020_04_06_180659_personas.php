<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Personas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->integer('id_persona')->primary()->unsigned();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('cargo');
            $table->integer('id_dependencia')->unsigned();
            $table->integer('id_entidad')->unsigned();
            $table->string('nit');
            $table->integer('nivel');
            $table->integer('extension');
            $table->string('activo');
            $table->timestamps();       

            $table->foreign('id_entidad')->references('id_entidad')->on('entidads');
            $table->foreign('id_dependencia')->references('id_dependencia')->on('dependencias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas');
    }
}
