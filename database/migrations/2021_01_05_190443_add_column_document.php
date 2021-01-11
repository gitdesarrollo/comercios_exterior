<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDocument extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->bigInteger('idTipoDocumento')->after('id_status')->unsigned()->default(1);
            $table->date('dateOfAdmission')->after('idTipoDocumento')->nullable();
            $table->date('ReceptionDate')->after('dateOfAdmission')->nullable();
            $table->foreign('idTipoDocumento')->references('id')->on('type_documents');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn('idTipoDocumento');
            $table->dropColumn('dateOfAdmission');
            $table->dropColumn('ReceptionDate');
        });
    }
}
