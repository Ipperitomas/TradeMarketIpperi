<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        Schema::dropIfExists('inventory_cabecera');
        

        Schema::create('inventory_cabecera', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha');
            $table->string('nro_comprobante',255);
            $table->enum('tipo_accion',['COMPRA','VENTA'])->default('COMPRA');
            $table->json('data')->nullable();
            $table->timestamps();
        });
        
        Schema::dropIfExists('inventory_renglones');
        
        Schema::create('inventory_renglones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cabecera_id');
            $table->foreign('cabecera_id')->references('id')->on('inventory_cabecera');
            $table->unsignedBigInteger('articulo_id');
            $table->foreign('articulo_id')->references('id')->on('articles');
            $table->bigInteger('cantidad');
            $table->timestamps();
        });
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('inventory_cabecera');
        Schema::dropIfExists('inventory_renglones');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
