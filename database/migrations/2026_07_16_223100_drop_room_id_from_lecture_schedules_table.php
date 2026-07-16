<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * قرار المستخدم: إلغاء فكرة "القاعة" نهائياً - ما كانت مربوطة بأي نظام حجز قاعات فعلي
 * أصلاً، مجرد نص حر بيتكتب يدوي، فتقرر حذفها بدل ما تفضل حقل بلا فايدة.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lecture_schedules', function (Blueprint $table) {
            $table->dropColumn('room_id');
        });
    }

    public function down(): void
    {
        Schema::table('lecture_schedules', function (Blueprint $table) {
            $table->string('room_id')->nullable()->after('end_time');
        });
    }
};
