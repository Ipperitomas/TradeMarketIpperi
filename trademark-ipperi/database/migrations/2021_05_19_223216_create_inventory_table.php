<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        //Schema::dropIfExists('inventory');

        Schema::create('inventory', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('article_id');
            $table->string('nombre',255)->nullable(NULL);
            $table->bigInteger('cantidad');
            $table->float('p_unitario')->nullable(NULL);
            $table->float('p_total')->nullable(NULL);
            $table->enum('tipo_accion',['COMPRA','VENTA'])->default('COMPRA');
            $table->text('data',500)->nullable(NULL);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory');
    }
}
