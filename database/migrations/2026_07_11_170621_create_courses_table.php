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
    Schema::create('courses', function (Blueprint $table) {

        $table->id();

        // رمز المقرر
        $table->string('code', 20)->unique();

        // اسم المقرر
        $table->string('name');

        // عدد الساعات
        $table->unsignedTinyInteger('credit_hours');

        // وصف المقرر
        $table->text('description')->nullable();

        // حالة المقرر
        $table->boolean('is_active')->default(true);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
