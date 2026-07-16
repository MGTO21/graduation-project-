<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LectureRecording extends Model
{
    protected $fillable = [
        'lecture_id',
        'stored_name',
        'video_path',
        'duration',
        'file_size',
    ];

    /**
     * المحاضرة التي ينتمي إليها التسجيل
     */
    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }
}
