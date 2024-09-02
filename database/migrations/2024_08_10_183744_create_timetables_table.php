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
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id'); // Foreign key for the teacher
            $table->unsignedBigInteger('section_id'); // Foreign key for the class
            $table->string('class_name');
            $table->string('timing'); // Starting hour
            $table->string('day'); // Day of the week
            $table->timestamps();
        
            // Foreign key constraints
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
        
            // Ensure that each class and teacher has only one timetable per day and time slot
            // $table->unique(['teacher_id', 'section_id', 'day', 'timing']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
