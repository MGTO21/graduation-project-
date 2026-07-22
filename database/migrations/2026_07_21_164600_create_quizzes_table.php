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
    Schema::create('quizzes', function (Blueprint $table) {

        $table->id();

        // المقرر المطروح اللي السؤال تابع ليه - كل طلاب المقرر يشوفوا نفس السؤال
        $table->foreignId('course_offering_id')
              ->constrained()
              ->cascadeOnDelete();

        // المحاضر اللي أنشأ السؤال
        $table->foreignId('lecturer_id')
              ->constrained('users')
              ->cascadeOnDelete();

        // نص السؤال
        $table->text('question');

        // الخيارات الأربعة
        $table->string('option_a');
        $table->string('option_b');
        $table->string('option_c');
        $table->string('option_d');

        // الخيار الصحيح
        $table->enum('correct_option', ['a', 'b', 'c', 'd']);

        // مدة الإجابة بالثواني - من لحظة الإطلاق
        $table->unsignedInteger('duration_seconds')->default(60);

        // وقت إطلاق السؤال فعلياً (يفضل فاضي طول ما السؤال لسه draft)
        $table->timestamp('started_at')->nullable();

        // حالة السؤال: draft لسه ما اتطلقش، active شغال دلوقتي، ended خلص وقته
        $table->enum('status', ['draft', 'active', 'ended'])->default('draft');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
