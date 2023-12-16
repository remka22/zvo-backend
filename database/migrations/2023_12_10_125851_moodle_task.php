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
        Schema::create('moodle_task', function (Blueprint $table) {
            $table->id();
            $table->string('link');
            $table->string('name');
            $table->bigInteger('course_id', false, true);
            $table->foreign('course_id')->references('id')->on('moodle_course');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moodle_task');
    }
};
