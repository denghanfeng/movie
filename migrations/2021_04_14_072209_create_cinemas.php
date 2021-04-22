<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCinemas extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cinemas', function (Blueprint $table) {
            $table->bigIncrements('cinemaId');
            $table->integer('cityId',)->nullable()->default(0)->comment('城市ID');
            $table->string('cinemaName',255)->nullable()->default('')->comment('影院名称');
            $table->string('address',255)->nullable()->default('')->comment('影院地址');
            $table->double('latitude',9,5)->nullable()->default(0)->comment('纬度');
            $table->double('longitude',9,5)->nullable()->default(0)->comment('经度');
            $table->string('phone',255)->nullable()->default('')->comment('影院电话');
            $table->string('regionName',36)->nullable()->default('')->comment('地区名称');
            $table->integer('areaId')->nullable()->default(0)->comment('地区ID');
            $table->boolean('isAcceptSoonOrder')->nullable()->default(0)->comment('是否支持秒出票，0为不支持，1为支持');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cinemas');
    }
}
