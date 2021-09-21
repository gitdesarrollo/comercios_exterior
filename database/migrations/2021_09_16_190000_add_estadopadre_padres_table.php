<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstadopadrePadresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('padres', function (Blueprint $table) {
            $table->bigInteger('estatus')->after('descripcion')->unsigned()->default(1);
            $table->foreign('estatus')->references('id')->on('estadopadre');
        });
        
            
            

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
