<?php

namespace App\Http\Controllers\Lecturer;

use App\Models\CourseOffering;
use App\Models\Lecture;
use App\Models\LectureSchedule;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * أيام الأسبوع الدراسية مع ترجمتها العربية
     */
    private array $days = [
        'Saturday'  => 'السبت',
        'Sunday'    => 'الأحد',
        'Monday'    => 'الاثنين',
        'Tuesday'   => 'الثلاثاء',
        'Wednesday' => 'الأربعاء',
        'Thursday'  => 'الخميس',
    ];

    /**
     * لوحة المحاضر: جدوله الأسبوعي ومقرراته المطروحة
     */
    public function index()
    {
        $lecturer = auth()->user();

        // المقررات التي يدرسها المحاضر
        $courseOfferings = CourseOffering::with([
            'course',
            'department',
            'semester',
        ])
        ->where('lecturer_id', $lecturer->id)
        ->where('is_active', true)
        ->get();

        // الجدول الأسبوعي لمقررات المحاضر مجمعاً حسب اليوم
        $schedules = LectureSchedule::with([
            'courseOffering.course',
            'courseOffering.department',
            'courseOffering.semester',
        ])
        ->whereIn('course_offering_id', $courseOfferings->pluck('id'))
        ->orderBy('start_time')
        ->get()
        ->groupBy('day');

        // محاضرات اليوم فقط (مربوطة بمواعيد جدول المحاضر) - نجيبها عشان نعرف لكل موعد
        // اليوم هل البث بدأ، انتهى، أو لسه ما بدأش - ونعرض الزر المناسب في الواجهة
        $todayLectures = Lecture::whereIn('lecture_schedule_id', $schedules->flatten()->pluck('id'))
            ->where('lecture_date', now()->toDateString())
            ->get()
            ->keyBy('lecture_schedule_id');

        // مواعيد اليوم فقط - نعرضها في بطاقة بارزة أعلى اللوحة عشان زر "بدء البث" يكون
        // واضح وسهل الوصول له، مش مدفون جوا شبكة الجدول الأسبوعي كلها
        $todaySchedules = $schedules->get(now()->format('l'), collect());

        // بث مباشر شغال حالياً لأي من مقررات المحاضر، بغض النظر عن يوم الموعد المرتبط فيه -
        // محتاجينها عشان زر "بث طارئ" (لأي وقت) يعرف يتحول لـ "متابعة البث" لو أصلاً في بث شغال
        $liveLecturesByOffering = Lecture::with('lectureSchedule')
            ->whereIn('lecture_schedule_id', $schedules->flatten()->pluck('id'))
            ->where('status', 'live')
            ->get()
            ->keyBy(fn (Lecture $lecture) => $lecture->lectureSchedule->course_offering_id);

        $days = $this->days;

        return view('lecturer.dashboard', compact(
            'lecturer', 'courseOfferings', 'schedules', 'days',
            'todayLectures', 'todaySchedules', 'liveLecturesByOffering'
        ));
    }
}
