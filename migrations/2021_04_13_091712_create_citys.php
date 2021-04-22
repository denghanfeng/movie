<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCitys extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('citys', function (Blueprint $table) {
            $table->bigIncrements('cityId');
            $table->string('pinYin',4)->nullable()->default('')->comment('pinYin');
            $table->string('regionName',16)->nullable()->default('')->comment('城市名');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citys');
    }
}
