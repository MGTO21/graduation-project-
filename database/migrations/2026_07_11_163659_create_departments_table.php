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
    Schema::create('departments', function (Blueprint $table) {

        // المفتاح الأساسي
        $table->id();

        // اسم القسم
        $table->string('name', 100)->unique();

        // رمز القسم (اختياري)
        $table->string('code', 20)->unique();

        // وصف القسم
        $table->text('description')->nullable();

        // حالة القسم
        $table->boolean('is_active')->default(true);

        // تاريخ الإنشاء والتعديل
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
