<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCityAreas extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('city_areas', function (Blueprint $table) {
            $table->bigIncrements('areaId');
            $table->integer('cityId',)->nullable()->default(0)->comment('城市ID');
            $table->string('areaName',32)->nullable()->default('')->comment('地区名称');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city_areas');
    }
}
