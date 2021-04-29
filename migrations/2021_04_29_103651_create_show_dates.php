<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateShowDates extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('show_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('filmId')->comment('影片id');
            $table->integer('cinemaId')->default(0)->comment('影院id');
            $table->integer('cityId')->nullable()->default(0)->comment('城市ID');
            $table->char('date',10)->nullable()->default('')->comment('城市ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('show_dates');
    }
}
