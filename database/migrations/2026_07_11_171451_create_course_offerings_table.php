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
    Schema::create('course_offerings', function (Blueprint $table) {

        $table->id();

        // المقرر
        $table->foreignId('course_id')
              ->constrained()
              ->cascadeOnDelete();

        // القسم
        $table->foreignId('department_id')
              ->constrained()
              ->cascadeOnDelete();

        // السمستر
        $table->foreignId('semester_id')
              ->constrained()
              ->cascadeOnDelete();

        // المحاضر
        $table->foreignId('lecturer_id')
              ->constrained('users')
              ->cascadeOnDelete();

        // هل المقرر مطروح حاليا
        $table->boolean('is_active')->default(true);

        $table->timestamps();

        // منع تكرار نفس طرح المقرر
        // الاسم القصير ضروري: الاسم التلقائي يتجاوز حد MySQL (64 حرفاً)
        $table->unique([
            'course_id',
            'department_id',
            'semester_id',
            'lecturer_id'
        ], 'course_offerings_unique');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_offerings');
    }
};
