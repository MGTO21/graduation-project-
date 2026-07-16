<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $departments = Department::orderBy('id')->get();

    return view('admin.departments.index', compact('departments'));
}


    public function create()
{
    return view('admin.departments.create');
}

    /**
     * Show the form for creating a new resource.
     */
   // public function create()
  //  {
        //
   // }

    /**
     * Store a newly created resource in storage.
     */
    //public function store(Request $request)
   // {
        //
   // }



public function store(Request $request)
{
    $request->validate([
    'name' => 'required|string|max:255|unique:departments,name',
    'code' => 'required|string|max:10|unique:departments,code',
]);

    Department::create([
        'name' => $request->name,
        'code' => strtoupper($request->code),
    ]);

    return redirect()
        ->route('admin.departments.index')
        ->with('success', 'تم إضافة القسم بنجاح.');
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
    public function edit(Department $department)
{
    return view('admin.departments.edit', compact('department'));
}

public function update(Request $request, Department $department)
{
    $request->validate([
        'name' => 'required|unique:departments,name,' . $department->id,
        'code' => 'required|unique:departments,code,' . $department->id,
    ]);

    $department->update([
        'name' => $request->name,
        'code' => strtoupper($request->code),
    ]);

    return redirect()->route('admin.departments.index')
        ->with('success', 'تم تعديل القسم بنجاح.');
}

public function destroy(Department $department)
{
    $department->delete();

    return redirect()->route('admin.departments.index')
        ->with('success', 'تم حذف القسم بنجاح.');
}
}
