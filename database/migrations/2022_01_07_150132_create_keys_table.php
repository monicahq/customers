<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('product');
            $table->string('friendly_name');
            $table->string('plan_name');
            $table->integer('price');
            $table->string('frequency');
            $table->timestamps();
        });

        Schema::create('licence_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('user_id');
            $table->text('key');
            $table->datetime('valid_until_at');
            $table->timestamps();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
        Schema::dropIfExists('licence_keys');
    }
}
