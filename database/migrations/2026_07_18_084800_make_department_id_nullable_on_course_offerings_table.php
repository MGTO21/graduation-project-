<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * بعض المقررات مشتركة بين كل الأقسام (مثلاً مادة عامة زي "مهارات الحاسوب" أو "اللغة
 * الإنجليزية")، مش تابعة لقسم واحد بعينه. عشان نقدر نطرحها من غير ما نضطر نكررها لكل
 * قسم لوحده، خلينا department_id في course_offerings ينفع يكون NULL - ولو كان NULL
 * معناها "طرح المقرر ده شامل كل الأقسام" (نتحقق من الشرط ده في الكنترولرز اللي بتفلتر
 * حسب قسم الطالب).
 *
 * ملاحظة تقنية: القيد unique المركّب (course_offerings_unique) كان مستخدم في MySQL
 * كفهرس مساند لأكتر من مفتاح أجنبي في نفس الوقت (course_id باعتباره أول عمود فيه)،
 * فلازم نفك كل المفاتيح الأجنبية الأربعة على الجدول قبل ما نقدر نحذفه، وبعدين نرجعهم
 * كلهم مع بعض بعد ما نضيف العمود من جديد nullable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['semester_id']);
            $table->dropForeign(['lecturer_id']);
            $table->dropUnique('course_offerings_unique');
        });

        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });

        Schema::table('course_offerings', function (Blueprint $table) {
            $table->foreignId('department_id')
                  ->nullable()
                  ->after('course_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->foreign('semester_id')->references('id')->on('semesters')->cascadeOnDelete();
            $table->foreign('lecturer_id')->references('id')->on('users')->cascadeOnDelete();

            $table->unique([
                'course_id',
                'department_id',
                'semester_id',
                'lecturer_id',
            ], 'course_offerings_unique');
        });
    }

    public function down(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['semester_id']);
            $table->dropForeign(['lecturer_id']);
            $table->dropUnique('course_offerings_unique');
        });

        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });

        Schema::table('course_offerings', function (Blueprint $table) {
            $table->foreignId('department_id')
                  ->after('course_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->foreign('semester_id')->references('id')->on('semesters')->cascadeOnDelete();
            $table->foreign('lecturer_id')->references('id')->on('users')->cascadeOnDelete();

            $table->unique([
                'course_id',
                'department_id',
                'semester_id',
                'lecturer_id',
            ], 'course_offerings_unique');
        });
    }
};
