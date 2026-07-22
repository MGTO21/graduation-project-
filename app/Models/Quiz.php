<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'course_offering_id',
        'lecturer_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'duration_seconds',
        'started_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
        ];
    }

    /**
     * المقرر المطروح اللي السؤال تابع ليه
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * المحاضر اللي أنشأ السؤال
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * إجابات الطلاب على هذا السؤال
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    /**
     * الوقت المتبقي بالثواني من لحظة النداء - مش من لحظة فتح الصفحة عند الطالب.
     * لازم نحسبها كده عشان لو طالب فتح الصفحة متأخر (بعد 20 ثانية من الإطلاق مثلاً)
     * يشوف الوقت الصحيح المتبقي فعلياً، مش يبدأ عداد جديد من مدة السؤال كاملة.
     */
    public function secondsRemaining(): int
    {
        if (! $this->started_at) {
            return $this->duration_seconds;
        }

        // بنستخدم فرق الـ timestamps مباشرة بدل ->diffInSeconds() لأن Carbon بيرجع
        // فرق بإشارة سالبة أحياناً حسب اتجاه المقارنة، وده كان بيلخبط الحساب هنا
        $elapsed = now()->getTimestamp() - $this->started_at->getTimestamp();

        return max(0, $this->duration_seconds - $elapsed);
    }

    /**
     * لو السؤال active بس وقته خلص فعلياً، نرجّعه ended دلوقتي. بما إننا بنعتمد على
     * polling وما عندنا queue/job مجدول، أنسب مكان نتأكد فيه من انتهاء الوقت هو أول
     * ما حد (محاضر أو طالب) يسأل عن حالة السؤال - يعني التحديث بيصير "كسول" (lazy)
     * عند أول طلب بعد وقت الانتهاء، مش لحظة الانتهاء نفسها بالظبط، وده كافي جداً
     * لأن أطول فرق ممكن هو نفس دورة الـ polling (3 ثواني).
     */
    public function refreshStatusIfExpired(): void
    {
        if ($this->status === 'active' && $this->secondsRemaining() <= 0) {
            $this->update(['status' => 'ended']);
        }
    }

    /**
     * عدد الطلاب اللي المفروض يشوفوا السؤال ده: نفس شرط الأهلية المستخدم في مكان
     * الشات - سمستر المقرر لازم يطابق، والقسم يطابق إلا لو المقرر مشترك بين كل الأقسام
     */
    public function eligibleStudentsCount(): int
    {
        $offering = $this->courseOffering;

        return User::where('role', 'student')
            ->where('semester_id', $offering->semester_id)
            ->when($offering->department_id !== null, function ($query) use ($offering) {
                $query->where('department_id', $offering->department_id);
            })
            ->count();
    }
}
