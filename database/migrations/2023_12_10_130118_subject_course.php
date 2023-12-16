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
        Schema::create('subject_course', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('program_id', false, true);
            $table->integer('number_course');
            $table->bigInteger('teacher_course_id', false, true);
            $table->string('comment');

            $table->foreign('teacher_course_id')->references('id')->on('teacher_course');
            $table->foreign('program_id')->references('id')->on('learn_program');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_course');
    }
};
