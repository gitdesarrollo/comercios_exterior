<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTracings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracings', function (Blueprint $table) {
            $table->String('instruccion')->after('estado');
            $table->String('instruccion_ministro')->after('instruccion');
            $table->String('id_vice')->after('instruccion_ministro');

            $table->foreign('id_vice')->references('id')->on('viceministerios');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracings', function (Blueprint $table) {
            $table->dropColumn('instruccion');
            $table->dropColumn('instruccion_ministro');
            $table->dropColumn('id_vice');
        });
    }
}
