<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LecturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $lecturers = User::where('role', 'lecturer')
        ->orderBy('name')
        ->get();

    return view('admin.lecturers.index', compact('lecturers'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $departments = Department::orderBy('name')->get();

    return view('admin.lecturers.create', compact('departments'));
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
        'password'      => 'required|min:8',
    ]);

    User::create([
        'role'          => 'lecturer',
        'university_id' => $request->university_id,
        'name'          => $request->name,
        'email'         => $request->email,
        'phone'         => $request->phone,
        'department_id' => $request->department_id,
        'semester_id'   => null,
        'profile_image' => null,
        'is_active'     => true,
        'password'      => Hash::make($request->password),
    ]);

    return redirect()
        ->route('admin.lecturers.index')
        ->with('success', 'تمت إضافة المحاضر بنجاح.');
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
    public function edit(User $lecturer)
{
    $departments = Department::orderBy('name')->get();

    return view('admin.lecturers.edit', compact(
        'lecturer',
        'departments'
    ));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $lecturer)
{
    $request->validate([
        'university_id' => 'required|unique:users,university_id,' . $lecturer->id,
        'name'          => 'required|string|max:255',
        'email'         => 'required|email|unique:users,email,' . $lecturer->id,
        'phone'         => 'nullable|string|max:20',
        'department_id' => 'required|exists:departments,id',
    ]);

    $lecturer->update([
        'university_id' => $request->university_id,
        'name'          => $request->name,
        'email'         => $request->email,
        'phone'         => $request->phone,
        'department_id' => $request->department_id,
    ]);

    return redirect()
        ->route('admin.lecturers.index')
        ->with('success', 'تم تحديث بيانات المحاضر بنجاح.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $lecturer)
{
    $lecturer->delete();

    return redirect()
        ->route('admin.lecturers.index')
        ->with('success', 'تم حذف المحاضر بنجاح.');
}
}
