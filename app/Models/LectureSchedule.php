<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LectureSchedule extends Model
{
    protected $fillable = [
    'course_offering_id',
    'day',
    'start_time',
    'end_time',
    'is_active',
];

    /**
     * المقرر المطروح
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * المحاضرات الفعلية
     */
    public function lectures(): HasMany
    {
        return $this->hasMany(Lecture::class);
    }
}