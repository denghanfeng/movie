<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateFilmes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('filmes', function (Blueprint $table) {
            $table->bigIncrements('filmId')->comment('影片id');
            $table->integer('grade')->default(0)->comment('评分');
            $table->string('name')->default('')->comment('影片名');
            $table->integer('duration')->default(0)->comment('时长，分钟');
            $table->timestamp('publishDate')->comment('影片上映日期');
            $table->string('director')->comment('导演');
            $table->string('cast')->comment('主演');
            $table->string('intro',1000)->comment('简介');
            $table->string('versionTypes')->comment('上映类型');
            $table->string('language')->comment('语言');
            $table->string('filmTypes')->comment('影片类型');
            $table->string('pic')->comment('海报URL地址');
            $table->string('like')->comment(0)->comment('想看人数');
            $table->smallInteger('showStatus')->comment('放映状态：1 正在热映。2 即将上映');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filmes');
    }
}
