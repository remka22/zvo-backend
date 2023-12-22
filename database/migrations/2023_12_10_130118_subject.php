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
        Schema::create('subject', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('group_id', false, true);
            $table->integer('number_course');
            //$table->bigInteger('teacher_id', false, true);
            $table->bigInteger('subject_teacher_id', false, true)->nullable();
            //$table->bigInteger('teacher_course_id', false, true)->nullable();
            //$table->string('comment')->nullable();

            //$table->foreign('teacher_id')->references('id')->on('users');
            $table->foreign('group_id')->references('id')->on('groups');
            //$table->foreign('teacher_course_id')->references('id')->on('teacher_course');
            //$table->foreign('teacher_subject_id')->references('id')->on('teacher_subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject');
    }
};
