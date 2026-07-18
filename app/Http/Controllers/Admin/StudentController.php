<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use App\Models\Department;
use App\Models\Semester;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $students = User::where('role', 'student')
        ->when($request->filled('department_id'), fn ($query) => $query->where('department_id', $request->department_id))
        ->when($request->filled('semester_id'), fn ($query) => $query->where('semester_id', $request->semester_id))
        ->orderBy('name')
        ->get();

    $departments = Department::orderBy('name')->get();
    $semesters = Semester::orderBy('number')->get();

    return view('admin.students.index', compact('students', 'departments', 'semesters'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $departments = Department::orderBy('name')->get();

    $semesters = Semester::orderBy('number')->get();

    return view('admin.students.create', compact(
        'departments',
        'semesters'
    ));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'university_id' => 'required|unique:users,university_id',
        'name'          => 'required|string|max:255',
        'email'         => 'required|email|unique:users,email',
        'phone'         => 'nullable|string|max:20',
        'department_id' => 'required|exists:departments,id',
        'semester_id'   => 'required|exists:semesters,id',
        'password'      => 'required|min:8',
    ]);

    User::create([
        'role'          => 'student',
        'university_id' => $request->university_id,
        'name'          => $request->name,
        'email'         => $request->email,
        'phone'         => $request->phone,
        'department_id' => $request->department_id,
        'semester_id'   => $request->semester_id,
        'profile_image' => null,
        'is_active'     => true,
        'password'      => Hash::make($request->password),
    ]);

    return redirect()
        ->route('admin.students.index')
        ->with('success', 'تمت إضافة الطالب بنجاح.');
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
    public function edit(User $student)
{
    $departments = Department::orderBy('name')->get();

    $semesters = Semester::orderBy('number')->get();

    return view('admin.students.edit', compact(
        'student',
        'departments',
        'semesters'
    ));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $student)
{
    $request->validate([
        'university_id' => 'required|unique:users,university_id,' . $student->id,
        'name'          => 'required|string|max:255',
        'email'         => 'required|email|unique:users,email,' . $student->id,
        'phone'         => 'nullable|string|max:20',
        'department_id' => 'required|exists:departments,id',
        'semester_id'   => 'required|exists:semesters,id',
    ]);

    $student->update([
        'university_id' => $request->university_id,
        'name'          => $request->name,
        'email'         => $request->email,
        'phone'         => $request->phone,
        'department_id' => $request->department_id,
        'semester_id'   => $request->semester_id,
    ]);

    return redirect()
        ->route('admin.students.index')
        ->with('success', 'تم تحديث بيانات الطالب بنجاح.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $student)
{
    $student->delete();

    return redirect()
        ->route('admin.students.index')
        ->with('success', 'تم حذف الطالب بنجاح.');
}
}
