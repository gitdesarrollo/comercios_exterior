<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHasViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('rol')->unsigned();
            $table->bigInteger('permits')->unsigned();
            $table->bigInteger('estado')->unsigned();
            $table->timestamps();

            $table->foreign('rol')->references('id')->on('roles_users');
            $table->foreign('permits')->references('id')->on('view_users');
            $table->foreign('estado')->references('id')->on('estado_documentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_has_views');
    }
}
