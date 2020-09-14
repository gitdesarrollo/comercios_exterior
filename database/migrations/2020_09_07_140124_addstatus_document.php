<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddstatusDocument extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->bigInteger('id_status')->after('correlativo_documento')->unsigned();
            $table->bigInteger('idProfesion')->after('correlativo_documento')->unsigned();
            $table->foreign('id_status')->references('id')->on('estado_documentos');
            $table->foreign('idProfesion')->references('id')->on('profesiones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('id_status');
    }
}
