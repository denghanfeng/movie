<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddUpDiscountRateFromCinemas extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cinemas', function (Blueprint $table) {
            $table->double('upDiscountRate',4,2)->default(0)->comment('当价格大于等于39元时候');
            $table->double('downDiscountRate',4,2)->default(0)->comment('当价格小于39元时候');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cinemas', function (Blueprint $table) {
            $table->dropColumn('upDiscountRate');
            $table->dropColumn('downDiscountRate');
        });
    }
}
