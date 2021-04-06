<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cat_id_1');
            $table->unsignedBigInteger('cat_id_2');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('cat_id_1')
                ->references('id')
                ->on('cats')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('cat_id_2')
                ->references('id')
                ->on('cats')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matings');
    }
}
