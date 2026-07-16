<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LectureFile extends Model
{
    protected $fillable = [
        'lecture_id',
        'original_name',
        'stored_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    /**
     * المحاضرة التي ينتمي إليها الملف
     */
    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }
}
