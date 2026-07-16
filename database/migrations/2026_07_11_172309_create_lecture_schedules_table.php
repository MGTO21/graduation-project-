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
    Schema::create('lecture_schedules', function (Blueprint $table) {

        $table->id();

        // المقرر المطروح
        $table->foreignId('course_offering_id')
              ->constrained()
              ->cascadeOnDelete();

        // يوم المحاضرة
        $table->enum('day', [
            'Saturday',
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday'
        ]);

        // وقت البداية
        $table->time('start_time');

        // وقت النهاية
        $table->time('end_time');

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecture_schedules');
    }
};
