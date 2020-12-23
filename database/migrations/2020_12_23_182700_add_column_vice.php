<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnVice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dependencias', function (Blueprint $table) {
            $table->bigInteger('idVice')->after('idEstado')->unsigned()->default(1);
            $table->foreign('idVice')->references('id')->on('viceministerios');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dependencias', function (Blueprint $table) {
            $table->dropColumn('idVice');
        });
    }
}
