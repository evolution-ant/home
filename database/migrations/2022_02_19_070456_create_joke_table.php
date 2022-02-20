<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJokeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joke', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content')->comment('内容');
            $table->integer('type_id')->comment('分类 id')->nullable();
            $table->string('description')->comment('描述')->nullable();
            $table->string('remark')->comment('备注')->nullable();
            $table->integer('reading_times')->comment('阅读次数');
            $table->integer('importance')->comment('重要性');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('joke');
    }
}
