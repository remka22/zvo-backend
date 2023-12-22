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
        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subject_id', false, true);
            $table->bigInteger('teacher_id', false, true);
            $table->bigInteger('teacher_course_id', false, true)->nullable();
            $table->string('comment')->nullable();

            $table->foreign('teacher_id')->references('id')->on('users');
            $table->foreign('teacher_course_id')->references('id')->on('teacher_course');
            $table->foreign('subject_id')->references('id')->on('subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_teacher');
    }
};
