<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('interesado');
            $table->string('correlativo_documento');
            // $table->bigInteger('idPersona')->unsigned();
            $table->string('folios');
            $table->string('descripcion',10000);
            $table->bigInteger('id_status')->unsigned();
            $table->string('correlativo_externo',200);
            $table->timestamps();
            
        
            $table->foreign('id_status')->references('id')->on('estado_documentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos');
    }
}
