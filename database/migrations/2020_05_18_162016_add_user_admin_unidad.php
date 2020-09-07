<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserAdminUnidad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('admin')->after('password');
            $table->bigInteger('id_unidad')->after('admin')->unsigned();
            $table->bigInteger('idEstado')->after('admin')->unsigned();
            
            $table->foreign('id_unidad')->references('id_dependencia')->on('dependencias');
            $table->foreign('idEstado')->references('id')->on('estado_documentos');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin');
            $table->dropColumn('id_unidad');
            $table->dropColumn('idEstado');
        });
    }
}
