<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $courses = Course::orderBy('code')->get();

    return view('admin.courses.index', compact('courses'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return view('admin.courses.create');
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'code' => 'required|unique:courses,code',
        'name' => 'required',
        'credit_hours' => 'required|integer|min:1|max:6',
        'description' => 'nullable',
    ]);

    Course::create([
        'code' => strtoupper($request->code),
        'name' => $request->name,
        'credit_hours' => $request->credit_hours,
        'description' => $request->description,
        'is_active' => true,
    ]);

    return redirect()
        ->route('admin.courses.index')
        ->with('success', 'تمت إضافة المقرر بنجاح.');
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
    public function edit(Course $course)
{
    return view('admin.courses.edit', compact('course'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
{
    $request->validate([
        'code' => 'required|unique:courses,code,' . $course->id,
        'name' => 'required',
        'credit_hours' => 'required|integer|min:1|max:6',
        'description' => 'nullable',
    ]);

    $course->update([
        'code' => strtoupper($request->code),
        'name' => $request->name,
        'credit_hours' => $request->credit_hours,
        'description' => $request->description,
    ]);

    return redirect()
        ->route('admin.courses.index')
        ->with('success', 'تم تحديث المقرر بنجاح.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
{
    $course->delete();

    return redirect()
        ->route('admin.courses.index')
        ->with('success', 'تم حذف المقرر بنجاح.');
}
}
