<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * جدول chat_messages أصلاً مربوط بـ lecture_id (يعني رسالة تخص محاضرة واحدة بعينها).
 * بس المطلوب شات جماعي لكل المقرر المطروح (course_offering) مش لكل محاضرة لحالها -
 * الطالب والمحاضر يتراسلوا في نفس المحادثة طول الترم، مش محادثة جديدة كل محاضرة.
 * فبدل ما نلخبط الجدول القديم، بنضيف عمود course_offering_id جديد (nullable) ونخلي
 * lecture_id نفسه nullable، ورسائل الشات الجماعي الجديدة بتستخدم course_offering_id
 * وتسيب lecture_id فاضي. الجدول أصلاً فاضي (الميزة دي ما كانت مربوطة بأي كنترولر قبل كده)
 * فمفيش بيانات قديمة نخاف عليها.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['lecture_id']);
            $table->dropColumn('lecture_id');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreignId('lecture_id')
                  ->nullable()
                  ->after('id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('course_offering_id')
                  ->nullable()
                  ->after('lecture_id')
                  ->constrained()
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['course_offering_id']);
            $table->dropColumn('course_offering_id');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['lecture_id']);
            $table->dropColumn('lecture_id');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreignId('lecture_id')
                  ->after('id')
                  ->constrained()
                  ->cascadeOnDelete();
        });
    }
};
