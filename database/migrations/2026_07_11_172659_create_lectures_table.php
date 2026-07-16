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
    Schema::create('lectures', function (Blueprint $table) {

        $table->id();

        // الجدول الأسبوعي
        $table->foreignId('lecture_schedule_id')
              ->constrained()
              ->cascadeOnDelete();

        // تاريخ المحاضرة
        $table->date('lecture_date');

        // وقت البداية الفعلي
        $table->timestamp('started_at')->nullable();

        // وقت النهاية الفعلية
        $table->timestamp('ended_at')->nullable();

        // حالة المحاضرة
        $table->enum('status', [
            'scheduled',
            'live',
            'ended',
            'cancelled'
        ])->default('scheduled');

        // معرف جلسة PeerJS
        $table->string('meeting_id')->nullable()->unique();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lectures');
    }
};
