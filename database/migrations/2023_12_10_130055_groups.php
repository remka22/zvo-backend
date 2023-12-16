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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('metodist_id', false, true);
            $table->bigInteger('program_id', false, true);
            $table->integer('number');

            $table->foreign('metodist_id')->references('id')->on('users');
            $table->foreign('program_id')->references('id')->on('learn_program');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
