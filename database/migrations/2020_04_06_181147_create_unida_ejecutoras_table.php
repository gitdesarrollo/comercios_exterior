<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnidaEjecutorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unida_ejecutoras', function (Blueprint $table) {
            $table->integer('id_unidad')->primary()->unsigned();
            $table->integer('id_entidad')->unsigned();
            $table->string('name');
            $table->timestamps();

            $table->foreign('id_entidad')->references('id_entidad')->on('entidads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unida_ejecutoras');
    }
}
