<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUserMail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_trackings', function (Blueprint $table) {
            $table->bigInteger('userCreate')->after('message')->nullable()->unsigned();
            $table->bigInteger('userReceives')->after('userCreate')->nullable()->unsigned();
            $table->bigInteger('estatus')->after('userReceives')->unsigned()->default(4);
            $table->foreign('userCreate')->references('id')->on('users');
            $table->foreign('userReceives')->references('id')->on('users');
            $table->foreign('estatus')->references('id')->on('estado_documentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_trackings', function (Blueprint $table) {
            $table->dropColumn('userCreate');
        });
    }
}
