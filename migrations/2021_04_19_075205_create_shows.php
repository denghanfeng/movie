<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateShows extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->string('showId')->comment('场次标识')->unique();
            $table->integer('cinemaId')->default(0)->comment('影院id');
            $table->string('hallName')->default('')->comment('影厅名');
            $table->integer('filmId')->default(0)->comment('影片id');
            $table->string('filmName')->default('')->comment('影片名字');
            $table->integer('duration')->default(0)->comment('时长,分钟');
            $table->timestamp('showTime')->comment('放映时间');
            $table->timestamp('stopSellTime')->comment('停售时间');
            $table->string('showVersionType')->comment('场次类型');
            $table->integer('netPrice')->comment('参考价，单位：分');
            $table->string('language')->comment('语言');
            $table->string('planType')->comment('影厅类型 2D 3D');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shows');
    }
}
