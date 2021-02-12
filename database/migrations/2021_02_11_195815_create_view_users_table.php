<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('view_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description',500);
            $table->bigInteger('idEstado')->unsigned();;
            $table->timestamps();

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
        Schema::dropIfExists('view_users');
    }
}
