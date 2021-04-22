<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('uid');
            $table->string('nickname',50)->nullable()->default('')->comment('昵称');
            $table->string('headimgurl',255)->nullable()->default('')->comment('头像');
            $table->string('openid',50)->nullable()->default('')->comment('openid');
            $table->string('mini_openid',50)->nullable()->default('')->comment('小程序openid');
            $table->string('unionid',50)->nullable()->default('')->comment('unionid');
            $table->integer('wx_id')->nullable()->default(0)->comment('公众号ID');
            $table->integer('accounts_id')->nullable()->default(0)->comment('关联的账户Id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
