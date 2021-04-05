<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('upload_files', function (Blueprint $table) {
            $table->bigInteger('estatus')->after('formato')->unsigned()->default(4);
            
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
        Schema::table('upload_files', function (Blueprint $table) {
            $table->dropColumn('estatus');
        });
    }
}
