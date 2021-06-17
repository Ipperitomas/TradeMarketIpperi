<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('articles');
        Schema::dropIfExists('rubros');
        
        Schema::create('rubros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre',255)->nullable();
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('rubro_id');
            $table->foreign('rubro_id')->references('id')->on('rubros');
            $table->string('nombre',255)->nullable();
            $table->string('descripcion',255)->nullable();
            $table->string('codigo',255)->nullable();
            $table->decimal('precio')->nullable();
            $table->integer('stock_max')->nullable();
            $table->integer('stock_min')->nullable();
            $table->text('caracteristicas',500)->nullable();
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('articles');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
