<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIdpadresDocumento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('documentos', function (Blueprint $table) {
            $table->bigInteger('idpadre')->after('correlativo_externo')->unsigned()->default(1);
            
            $table->foreign('idpadre')->references('id')->on('padres');

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
