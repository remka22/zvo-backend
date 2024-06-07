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
            $table->bigInteger('mira_id')->nullable();
            $table->bigInteger('moodle_id')->nullable();
            $table->bigInteger('group_id', false, true)->nullable();
            $table->boolean('isLogined')->default(false);
            $table->rememberToken();

            $table->foreign('role_id')->references('id')->on('roles');
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
