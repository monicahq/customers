<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('product', 512);
            $table->string('friendly_name', 512);
            $table->string('description', 1024);
            $table->string('plan_name', 128);
            $table->string('plan_id_on_paddle', 64);
            $table->integer('price');
            $table->string('frequency', 512);
            $table->timestamps();
        });

        Schema::create('licence_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('user_id');
            $table->string('key', 4096);
            $table->string('subscription_state', 64);
            $table->datetime('valid_until_at');
            $table->string('paddle_cancel_url', 512)->nullable();
            $table->string('paddle_update_url', 512)->nullable();
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
};
