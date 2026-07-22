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
    Schema::create('quiz_answers', function (Blueprint $table) {

        $table->id();

        // السؤال
        $table->foreignId('quiz_id')
              ->constrained()
              ->cascadeOnDelete();

        // الطالب المجيب
        $table->foreignId('student_id')
              ->constrained('users')
              ->cascadeOnDelete();

        // الخيار اللي اختاره الطالب
        $table->enum('selected_option', ['a', 'b', 'c', 'd']);

        // هل الاختيار ده مطابق للخيار الصحيح - بنحسبها وقت الحفظ عشان ما نحتاج
        // نرجع نقارن كل مرة لما نعرض النتائج
        $table->boolean('is_correct');

        // وقت الإجابة الفعلي
        $table->timestamp('answered_at');

        $table->timestamps();

        // قيد unique مركب على (quiz_id, student_id): هذا خط الدفاع الأول ضد تكرار
        // الإجابة، حتى قبل ما نوصل لأي تحقق في الكونترولر. لو جالنا طلبين قريبين من
        // نفس الطالب (مثلاً دبل-كليك أو تبويبين مفتوحين) القاعدة نفسها ترفض الإدخال
        // الثاني برسالة duplicate entry، فما نعتمد على الكود لوحده يمنع التكرار
        $table->unique(['quiz_id', 'student_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
};
