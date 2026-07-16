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
        Schema::create('users', function (Blueprint $table) {

    $table->id();
    
    // نوع المستخدم
    $table->enum('role', ['admin', 'lecturer', 'student']);

    // الرقم الجامعي أو الوظيفي (لتسجيل الدخول)
    $table->string('university_id')->unique();

    // الاسم الكامل
    $table->string('name');

    // البريد الإلكتروني
    $table->string('email')->unique()->nullable();

    // التحقق من البريد (اختياري)
    $table->timestamp('email_verified_at')->nullable();

    // رقم الجوال
    $table->string('phone', 20)->nullable();

    // قسم الطالب
    $table->foreignId('department_id')
          ->nullable()
          ->constrained()
          ->restrictOnDelete();

    // سمستر الطالب
    $table->foreignId('semester_id')
          ->nullable()
          ->constrained()
          ->restrictOnDelete();

    // الصورة الشخصية
    $table->string('profile_image')->nullable();

    // حالة الحساب
    $table->boolean('is_active')->default(true);

    // كلمة المرور
    $table->string('password');

    // Remember Me
    $table->rememberToken();

    $table->timestamps();
});

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
