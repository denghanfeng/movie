<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddFilmeNameFromOrders extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('filmeName',50)->default('')->comment('电影名称');
            $table->string('filmePic')->default('')->comment('电影图片');
            $table->string('cinemaName',100)->default('')->comment('影院名称');
            $table->string('cinemaAddress')->default('')->comment('影院名称');
            $table->double('latitude',9,5)->nullable()->default(0)->comment('纬度');
            $table->double('longitude',9,5)->nullable()->default(0)->comment('经度');
            $table->string('cinemaPhone',50)->nullable()->default('')->comment('影院电话');
            $table->integer('settle_amount')->nullable()->default(0)->comment('佣金');
            $table->integer('real_commission')->nullable()->default(0)->comment('实际佣金');
        });
    }

    /**
     * Reverse the migrations.w
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('filmeName');
            $table->dropColumn('filmePic');
            $table->dropColumn('cinemaName');
            $table->dropColumn('cinemaAddress');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('cinemaPhone');
            $table->dropColumn('settle_amount');
            $table->dropColumn('real_commission');
        });
    }
}
