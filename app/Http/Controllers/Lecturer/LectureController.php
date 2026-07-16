<?php

namespace App\Http\Controllers\Lecturer;

use App\Models\CourseOffering;
use App\Models\Lecture;
use App\Models\LectureFile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LectureController extends Controller
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
     * عرض محاضرات مقرر معين يملكه المحاضر
     */
    public function index(CourseOffering $courseOffering)
    {
        // حماية: المحاضر لا يصل إلا لمقرراته
        if ($courseOffering->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا المقرر.');
        }

        $courseOffering->load(['course', 'department', 'semester']);

        // محاضرات المقرر عبر مواعيد جدوله الأسبوعي
        $scheduleIds = $courseOffering->lectureSchedules()->pluck('id');

        $lectures = Lecture::with(['lectureSchedule', 'files'])
            ->whereIn('lecture_schedule_id', $scheduleIds)
            ->orderByDesc('lecture_date')
            ->get();

        $days = $this->days;

        return view('lecturer.lectures.index', compact('courseOffering', 'lectures', 'days'));
    }

    /**
     * نموذج إضافة محاضرة جديدة
     */
    public function create(CourseOffering $courseOffering)
    {
        // حماية: المحاضر لا يصل إلا لمقرراته
        if ($courseOffering->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا المقرر.');
        }

        $courseOffering->load('course');

        // مواعيد الجدول الأسبوعي الخاصة بهذا المقرر
        $schedules = $courseOffering->lectureSchedules()->orderBy('start_time')->get();

        $days = $this->days;

        return view('lecturer.lectures.create', compact('courseOffering', 'schedules', 'days'));
    }

    /**
     * حفظ محاضرة جديدة مع رفع ملفاتها
     *
     * ملاحظة: الحد الأقصى لحجم الملف 100MB.
     * إذا فشل الرفع لملفات كبيرة عدّل في ملف php.ini قيمتي:
     * upload_max_filesize = 100M
     * post_max_size = 120M
     */
    public function store(Request $request, CourseOffering $courseOffering)
    {
        // حماية: المحاضر لا يصل إلا لمقرراته
        if ($courseOffering->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا المقرر.');
        }

        $request->validate([
            'lecture_schedule_id' => 'required|exists:lecture_schedules,id',
            'lecture_date'        => 'required|date',
            'files'               => 'required|array|min:1',
            // mpga هو نوع mime الذي تُكتشف به ملفات mp3 أحياناً
            'files.*'             => 'file|mimes:mp4,mp3,mpga,pdf|max:102400',
        ], [
            'files.required'  => 'يجب رفع ملف واحد على الأقل.',
            'files.*.mimes'   => 'الأنواع المسموحة: فيديو mp4 أو صوت mp3 أو ملف pdf فقط.',
            'files.*.max'     => 'الحد الأقصى لحجم الملف 100 ميجابايت.',
        ]);

        // التأكد أن الموعد المختار يتبع لنفس المقرر
        $schedule = $courseOffering->lectureSchedules()
            ->where('id', $request->lecture_schedule_id)
            ->first();

        if (! $schedule) {
            return back()
                ->withInput()
                ->with('error', 'الموعد المختار لا يتبع لهذا المقرر.');
        }

        $lecture = Lecture::create([
            'lecture_schedule_id' => $schedule->id,
            'lecture_date'        => $request->lecture_date,
            'status'              => 'scheduled',
        ]);

        // رفع الملفات إلى storage/app/public/lectures وتسجيلها في lecture_files
        foreach ($request->file('files') as $file) {

            $storedName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $filePath = $file->storeAs('lectures', $storedName, 'public');

            LectureFile::create([
                'lecture_id'    => $lecture->id,
                'original_name' => $file->getClientOriginalName(),
                'stored_name'   => $storedName,
                'file_path'     => $filePath,
                'file_type'     => strtolower($file->getClientOriginalExtension()),
                'file_size'     => $file->getSize(),
            ]);
        }

        return redirect()
            ->route('lecturer.lectures.index', $courseOffering)
            ->with('success', 'تم إضافة المحاضرة ورفع ملفاتها بنجاح.');
    }

    /**
     * حذف محاضرة مع ملفاتها من التخزين
     */
    public function destroy(Lecture $lecture)
    {
        // حماية: المحاضر لا يحذف إلا محاضرات مقرراته
        if ($lecture->lectureSchedule->courseOffering->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بحذف هذه المحاضرة.');
        }

        $courseOffering = $lecture->lectureSchedule->courseOffering;

        // حذف الملفات الفعلية من التخزين قبل حذف السجل
        foreach ($lecture->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        $lecture->delete();

        return redirect()
            ->route('lecturer.lectures.index', $courseOffering)
            ->with('success', 'تم حذف المحاضرة وملفاتها بنجاح.');
    }

    /**
     * حذف ملف واحد من ملفات المحاضرة
     */
    public function destroyFile(LectureFile $lectureFile)
    {
        // حماية: المحاضر لا يحذف إلا ملفات محاضرات مقرراته
        if ($lectureFile->lecture->lectureSchedule->courseOffering->lecturer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بحذف هذا الملف.');
        }

        // حذف الملف الفعلي من التخزين
        Storage::disk('public')->delete($lectureFile->file_path);

        $lectureFile->delete();

        return back()->with('success', 'تم حذف الملف بنجاح.');
    }
}
