<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_send_id', false, true);
            $table->bigInteger('user_rec_id', false, true);
            $table->string('content');
            $table->bigInteger('send_date');
            $table->boolean('is_read');

            $table->foreign('user_send_id')->references('id')->on('users');
            $table->foreign('user_rec_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
