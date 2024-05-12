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
        Schema::create('needs_task', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subject_id', false, true);
            $table->bigInteger('task_id', false, true);

            $table->foreign('subject_id')->references('id')->on('subject_teacher');
            $table->foreign('task_id')->references('id')->on('moodle_task');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('needs_task');
    }
};
