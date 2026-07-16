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
    Schema::create('lecture_files', function (Blueprint $table) {

        $table->id();

        // المحاضرة
        $table->foreignId('lecture_id')
              ->constrained()
              ->cascadeOnDelete();

        // اسم الملف الأصلي
        $table->string('original_name');

        // اسم الملف داخل Storage
        $table->string('stored_name');

        // مسار الملف
        $table->string('file_path');

        // نوع الملف
        $table->string('file_type',50);

        // حجم الملف بالبايت
        $table->unsignedBigInteger('file_size');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecture_files');
    }
};
