<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Activos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activos', function (Blueprint $table) {
            $table->integer('id_activo')->primary()->unsigned(); 
            $table->string('fecha_fiscal');
            $table->integer('id_entidad')->unsigned();// ya
            $table->integer('id_unidad')->unsigned(); //ya
            $table->integer('id_grupo')->unsigned(); //ya
            $table->integer('id_categoria')->unsigned(); //ya
            $table->integer('id_seccion')->unsigned();// ya
            $table->integer('id_tipo')->unsigned(); // ya
            $table->integer('id_bien')->unsigned(); // ya
            $table->integer('id_estado')->unsigned();// ya
            $table->integer('id_producto')->unsigned();// ya
            $table->string('comentario')->nullable();
            $table->string('modelo')->nullable();
            $table->string('serie')->nullable();
            $table->string('marca')->nullable();
            $table->string('fecha_ingreso');
            $table->string('lugar_fisico');
            $table->integer('id_empleado')->unsigned();// ya
            $table->integer('id_dependencia')->unsigned(); // ya
            $table->integer('id_cuenta')->unsigned();// ya
            $table->integer('id_documento_respaldo')->unsigned();// ya
            $table->integer('id_secuencia')->unsigned();// ya
            $table->string('no_factura');
            $table->string('valor_costo');
            $table->string('serie_factura');
            $table->string('nit_proveedor');
            $table->string('alza');
            $table->string('baja');
            $table->string('codigo_sicoin');
            $table->integer('cantidad');
            $table->timestamps();
            
            $table->foreign('id_entidad')->references('id_entidad')->on('entidads');
            $table->foreign('id_unidad')->references('id_unidad')->on('unida_ejecutoras');
            $table->foreign('id_grupo')->references('id_grupo')->on('grupos');
            $table->foreign('id_categoria')->references('id_categoria')->on('categorias');
            $table->foreign('id_seccion')->references('id_seccion')->on('seccions');
            $table->foreign('id_tipo')->references('id_tipo')->on('tipos');
            $table->foreign('id_estado')->references('id_estadoP')->on('estado_productos');
            $table->foreign('id_producto')->references('id_producto')->on('productos');
            $table->foreign('id_empleado')->references('id_persona')->on('personas');
            $table->foreign('id_dependencia')->references('id_dependencia')->on('dependencias');
            $table->foreign('id_cuenta')->references('id_cuenta')->on('cuenta_activos');
            $table->foreign('id_documento_respaldo')->references('id_documento_respaldo')->on('documento_respaldos');
            $table->foreign('id_secuencia')->references('id_secuencia')->on('secuencias');



           


            // $table->integer('no_folio');
            // $table->integer('no_libro');
            // $table->string('documento_ingreso');
            // $table->integer('renglon');
            // $table->string('inventario_fisico');
            // $table->string('numero_movimiento');
            // $table->integer('cantidad_producto');
            // $table->bigInteger('id_categoria')->unsigned();
            // $table->bigInteger('id_estado_producto')->unsigned();
            
            
        });




    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activos');
    }
}
