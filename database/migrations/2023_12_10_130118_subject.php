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
            $table->bigInteger('subject_teacher_id', false, true)->nullable();
            
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('subject_teacher_id')->references('id')->on('subject_teacher');
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
