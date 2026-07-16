<?php

namespace App\Http\Controllers\Admin;

use App\Models\CourseOffering;
use App\Models\LectureSchedule;
use App\Models\Semester;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class LectureScheduleController extends Controller
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
     * عرض الجدول الأسبوعي مجمعاً حسب اليوم - لكل سمستر لوحده (تبويب) بدل ما كل السمسترات
     * تتخلط مع بعضها في نفس الشبكة، عشان الأدمن يقدر يشوف جدول فصل بعينه بوضوح
     */
    public function index(Request $request)
    {
        $semesters = Semester::orderBy('id')->get();

        // افتراضياً أول سمستر موجود، إلا لو الأدمن اختار سمستر معين من التبويب
        $selectedSemesterId = (int) $request->query('semester_id', $semesters->first()->id ?? 0);

        $schedules = LectureSchedule::with([
            'courseOffering.course',
            'courseOffering.department',
            'courseOffering.semester',
            'courseOffering.lecturer',
        ])
        ->whereHas('courseOffering', function ($query) use ($selectedSemesterId) {
            $query->where('semester_id', $selectedSemesterId);
        })
        ->orderBy('start_time')
        ->get()
        ->groupBy('day');

        $days = $this->days;

        return view('admin.lecture-schedules.index', compact('schedules', 'days', 'semesters', 'selectedSemesterId'));
    }

    /**
     * نموذج إضافة موعد جديد
     */
    public function create()
    {
        $courseOfferings = CourseOffering::with([
            'course',
            'department',
            'semester',
            'lecturer',
        ])
        ->where('is_active', true)
        ->get();

        $days = $this->days;

        return view('admin.lecture-schedules.create', compact('courseOfferings', 'days'));
    }

    /**
     * حفظ موعد جديد في الجدول
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'day'                => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday',
            'start_time'         => 'required|date_format:H:i',
            'end_time'           => 'required|date_format:H:i|after:start_time',
        ], [
            'end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية.',
        ]);

        try {
            LectureSchedule::create([
                'course_offering_id' => $request->course_offering_id,
                'day'                => $request->day,
                'start_time'         => $request->start_time,
                'end_time'           => $request->end_time,
                'is_active'          => true,
            ]);
        } catch (QueryException $e) {
            // القيد unique في جدول lecture_schedules يمنع تكرار نفس المقرر في نفس اليوم والوقت
            return back()
                ->withInput()
                ->with('error', 'يوجد تعارض: هذا المقرر لديه موعد مسجل مسبقاً في نفس اليوم ونفس وقت البداية.');
        }

        return redirect()
            ->route('admin.lecture-schedules.index')
            ->with('success', 'تم إضافة الموعد بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * نموذج تعديل الموعد
     */
    public function edit(LectureSchedule $lectureSchedule)
    {
        $courseOfferings = CourseOffering::with([
            'course',
            'department',
            'semester',
            'lecturer',
        ])
        ->where('is_active', true)
        ->get();

        $days = $this->days;

        return view('admin.lecture-schedules.edit', compact('lectureSchedule', 'courseOfferings', 'days'));
    }

    /**
     * تحديث الموعد
     */
    public function update(Request $request, LectureSchedule $lectureSchedule)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'day'                => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday',
            'start_time'         => 'required|date_format:H:i',
            'end_time'           => 'required|date_format:H:i|after:start_time',
        ], [
            'end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية.',
        ]);

        try {
            $lectureSchedule->update([
                'course_offering_id' => $request->course_offering_id,
                'day'                => $request->day,
                'start_time'         => $request->start_time,
                'end_time'           => $request->end_time,
            ]);
        } catch (QueryException $e) {
            // القيد unique في جدول lecture_schedules يمنع تكرار نفس المقرر في نفس اليوم والوقت
            return back()
                ->withInput()
                ->with('error', 'يوجد تعارض: هذا المقرر لديه موعد مسجل مسبقاً في نفس اليوم ونفس وقت البداية.');
        }

        return redirect()
            ->route('admin.lecture-schedules.index')
            ->with('success', 'تم تعديل الموعد بنجاح.');
    }

    /**
     * حذف الموعد
     */
    public function destroy(LectureSchedule $lectureSchedule)
    {
        $lectureSchedule->delete();

        return redirect()
            ->route('admin.lecture-schedules.index')
            ->with('success', 'تم حذف الموعد بنجاح.');
    }
}
