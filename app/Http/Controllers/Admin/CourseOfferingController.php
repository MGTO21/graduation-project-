<?php

namespace App\Http\Controllers\Admin;


use App\Models\Course;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;
use App\Models\CourseOffering;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseOfferingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $courseOffering = CourseOffering::with([
        'course',
        'department',
        'semester',
        'lecturer'
    ])->get();

    return view('admin.course-offerings.index', compact('courseOffering'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $courses = Course::where('is_active', true)->orderBy('name')->get();

    $departments = Department::orderBy('name')->get();

    $semesters = Semester::orderBy('number')->get();

    $lecturers = User::where('role', 'lecturer')
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('admin.course-offerings.create', compact(
        'courses',
        'departments',
        'semesters',
        'lecturers'
    ));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id',
        // department_id فاضي معناه "كل الأقسام" (مادة مشتركة) - مش خطأ تحقق
        'department_id' => 'nullable|exists:departments,id',
        'semester_id' => 'required|exists:semesters,id',
        'lecturer_id' => 'required|exists:users,id',
    ]);

    CourseOffering::create([
        'course_id' => $request->course_id,
        'department_id' => $request->department_id ?: null,
        'semester_id' => $request->semester_id,
        'lecturer_id' => $request->lecturer_id,
        'is_active' => true,
    ]);

    return redirect()
        ->route('admin.course-offerings.index')
        ->with('success', 'تمت إضافة طرح المقرر بنجاح.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseOffering $courseOffering)
{
    $courses = Course::where('is_active', true)
        ->orderBy('name')
        ->get();

    $departments = Department::orderBy('name')->get();

    $semesters = Semester::orderBy('number')->get();

    $lecturers = User::where('role', 'lecturer')
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('admin.course-offerings.edit', compact(
        'courseOffering',
        'courses',
        'departments',
        'semesters',
        'lecturers'
    ));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseOffering $courseOffering)
{
    $request->validate([
        'course_id'     => 'required|exists:courses,id',
        // department_id فاضي معناه "كل الأقسام" (مادة مشتركة) - مش خطأ تحقق
        'department_id' => 'nullable|exists:departments,id',
        'semester_id'   => 'required|exists:semesters,id',
        'lecturer_id'   => 'required|exists:users,id',
    ]);

    $courseOffering->update([
        'course_id'     => $request->course_id,
        'department_id' => $request->department_id ?: null,
        'semester_id'   => $request->semester_id,
        'lecturer_id'   => $request->lecturer_id,
    ]);

    return redirect()
        ->route('admin.course-offerings.index')
        ->with('success', 'تم تحديث طرح المقرر بنجاح.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseOffering $courseOffering)
{
    $courseOffering->delete();

    return redirect()
        ->route('admin.course-offerings.index')
        ->with('success', 'تم حذف طرح المقرر بنجاح.');
}
}
