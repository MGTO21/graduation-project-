<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['role','university_id','name', 'email','phone','department_id', 'semester_id','profile_image','is_active','password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

/**
 * القسم (للطالب فقط)
 */
public function department(): BelongsTo
{
    return $this->belongsTo(Department::class);
}

/**
 * السمستر (للطالب فقط)
 */
public function semester(): BelongsTo
{
    return $this->belongsTo(Semester::class);
}

/**
 * المقررات التي يدرسها المحاضر
 */
public function courseOfferings(): HasMany
{
    return $this->hasMany(CourseOffering::class, 'lecturer_id');
}

/**
 * رسائل الشات
 */
public function chatMessages(): HasMany
{
    return $this->hasMany(ChatMessage::class);
}



}
