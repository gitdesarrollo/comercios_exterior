<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_trackings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idTracings')->unsigned();
            $table->string('message',1000);
            $table->timestamps();

            $table->foreign('idTracings')->references('id')->on('tracings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_trackings');
    }
}
