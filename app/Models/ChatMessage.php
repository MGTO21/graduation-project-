<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'lecture_id',
        'course_offering_id',
        'user_id',
        'message',
    ];

    /**
     * المحاضرة التي أرسلت فيها الرسالة (اختياري - شات المقرر الجماعي ما بيربط برسالة بمحاضرة معينة)
     */
    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }

    /**
     * المقرر المطروح اللي تخصه الرسالة (شات جماعي لكل طلاب ومحاضر المقرر)
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * مرسل الرسالة
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}