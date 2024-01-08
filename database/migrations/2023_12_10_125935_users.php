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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('role_id', false, true);
            $table->string('fio');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->integer('mira_id')->nullable();
            $table->integer('moodle_id')->nullable();
            $table->foreign('role_id')->references('id')->on('roles');
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
