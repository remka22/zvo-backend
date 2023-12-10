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
        Schema::create('course_task', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subject_id', false, true);
            $table->bigInteger('task_id', false, true);

            $table->foreign('subject_id')->references('id')->on('subject_course');
            $table->foreign('task_id')->references('id')->on('moodle_task');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_task');
    }
};
