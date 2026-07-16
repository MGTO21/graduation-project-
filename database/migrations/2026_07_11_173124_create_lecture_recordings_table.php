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
    Schema::create('lecture_recordings', function (Blueprint $table) {

        $table->id();

        // المحاضرة
        $table->foreignId('lecture_id')
              ->constrained()
              ->cascadeOnDelete();

        // اسم الملف داخل Storage
        $table->string('stored_name');

        // مسار الفيديو
        $table->string('video_path');

        // مدة الفيديو بالثواني
        $table->unsignedInteger('duration')->nullable();

        // حجم الملف
        $table->unsignedBigInteger('file_size')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecture_recordings');
    }
};
