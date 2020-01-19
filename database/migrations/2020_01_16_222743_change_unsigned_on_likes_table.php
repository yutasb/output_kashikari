<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUnsignedOnLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->bigInteger('kashikari_id')->unsigned()->change(); // 符号無し属性に変更
            $table->foreign('kashikari_id')->references('id')->on('kashikaris'); // 外部キー参照

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('likes', function (Blueprint $table) {
            //
        });
    }
}