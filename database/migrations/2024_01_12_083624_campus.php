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
        // Schema::create('campus', function (Blueprint $table) {
        //     $table->id();
        //     $table->bigInteger('miraid', false, true)->nullable();
        //     $table->string('last_name')->nullable();
        //     $table->string('first_name')->nullable();
        //     $table->string('nomz')->nullable();
        //     $table->string('cohort')->nullable();
        //     $table->string('subfaculty')->nullable();
        //     $table->string('faculty')->nullable();
        //     $table->string('login')->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campus');
    }
};
