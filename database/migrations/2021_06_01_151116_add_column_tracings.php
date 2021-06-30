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
        });
    }
}
