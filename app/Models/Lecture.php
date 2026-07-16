<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lecture extends Model
{
    protected $fillable = [
        'lecture_schedule_id',
        'lecture_date',
        'started_at',
        'ended_at',
        'status',
        'meeting_id',
    ];

    /**
     * الجدول الأسبوعي
     */
    public function lectureSchedule(): BelongsTo
    {
        return $this->belongsTo(LectureSchedule::class);
    }

    /**
     * ملفات المحاضرة
     */
    public function files(): HasMany
    {
        return $this->hasMany(LectureFile::class);
    }

    /**
     * رسائل الشات
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * تسجيل المحاضرة
     */
    public function recording(): HasOne
    {
        return $this->hasOne(LectureRecording::class);
    }
}