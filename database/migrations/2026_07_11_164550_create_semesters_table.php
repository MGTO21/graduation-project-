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
    Schema::create('semesters', function (Blueprint $table) {

        $table->id();

        // رقم السمستر (1 - 10)
        $table->unsignedTinyInteger('number')->unique();

        // السنة الدراسية (1 - 5)
        $table->unsignedTinyInteger('academic_year');

        // اسم السمستر
        $table->string('name');

        // حالة السمستر
        $table->boolean('is_active')->default(true);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
