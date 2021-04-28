<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCityFilmes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('city_filmes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('cityId',)->nullable()->default(0)->comment('城市ID');
            $table->integer('filmId')->default(0)->comment('影片id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city_filmes');
    }
}
