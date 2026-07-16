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
    Schema::table('lecture_schedules', function (Blueprint $table) {

        $table->string('room_id')
              ->nullable()
              ->after('end_time');

        $table->boolean('is_active')
              ->default(true)
              ->after('room_id');

        $table->unique([
            'course_offering_id',
            'day',
            'start_time'
        ]);
    });
}

public function down(): void
{
    Schema::table('lecture_schedules', function (Blueprint $table) {

        $table->dropUnique([
            'course_offering_id',
            'day',
            'start_time'
        ]);

        $table->dropColumn([
            'room_id',
            'is_active'
        ]);
    });
}
};
