<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateOrders extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('thirdOrderId')->comment('接入方的订单号');
            $table->integer('uid')->default(0)->comment('用户id');
            $table->integer('cinemaId')->default(0)->comment('影院id');
            $table->integer('filmId')->default(0)->comment('影片id');
            $table->string('showId',255)->default('')->comment('场次标识');
            $table->string('appKey')->default('')->comment('下单appKey');
            $table->integer('orderStatus')->default(0)->comment('订单状态：2-受理中，3-待出票，4-已出票待结算，5-已结算，10-订单关闭');
            $table->string('orderStatusStr')->default('')->comment('订单状态说明');
            $table->integer('initPrice')->default(0)->comment('订单市场价：分');
            $table->integer('orderPrice')->default(0)->comment('订单成本价：分，接入方可拿次字段作为下单成本价');
            $table->string('seat')->default('')->comment('座位：英文逗号隔开');
            $table->integer('orderNum')->default(0)->comment('座位数');
            $table->string('reservedPhone')->default('')->comment('下单预留手机号码');
            $table->timestamp('readyTicketTime')->nullable()->comment('待出票时间');
            $table->timestamp('ticketTime')->nullable()->comment('出票时间');
            $table->timestamp('closeTime')->nullable()->comment('关闭时间');
            $table->string('closeCause')->nullable()->default('')->comment('关闭原因');
            $table->smallInteger('payType')->nullable()->default(0)->comment('支付方式');
            $table->string('payOrder',32)->nullable()->default('')->comment('支付订单号');
            $table->string('ticketCode')->nullable()->comment('取票码，type为1时，为字符串，type为2时，为取票码原始截图。 理论上一个取票码包含各字符串和原始截图， 原始截图可能不和字符串同步返回，有滞后性。');
            $table->string('ticketImage')->nullable()->comment('取票码原始截图');
            $table->boolean('acceptChangeSeat')->default(0)->comment('是否允许调座');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
