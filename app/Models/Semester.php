<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    protected $fillable = [
        'number',
        'academic_year',
        'name',
        'is_active'
    ];

    /**
     * الطلاب المسجلون في هذا السمستر
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * المقررات المطروحة في هذا السمستر
     */
    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }
}