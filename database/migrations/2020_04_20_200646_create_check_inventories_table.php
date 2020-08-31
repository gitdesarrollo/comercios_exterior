<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_inventories', function (Blueprint $table) {
            $table->integer('id_inventory')->primary()->unsigned();
            $table->integer('id_bien')->unsigned();
            $table->integer('fisico');
            $table->timestamps();

            /* $table->foreign('id_bien')->references('activos')->on('id_activo'); */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_inventories');
    }
}
