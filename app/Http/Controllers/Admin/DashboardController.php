<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Department;
use App\Models\Course;
use App\Models\Lecture;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
{
    // عمود department_id في users مستخدم للطلاب والمحاضرين مع بعض، فلازم نحدد
    // role=student جوا الـ withCount نفسه، وإلا العدّاد هيحسب المحاضرين برضو
    $studentsByDepartment = Department::withCount(['students' => function ($query) {
        $query->where('role', 'student');
    }])
    ->orderBy('name')
    ->get();

    return view('admin.dashboard', [
        'studentsCount'   => User::where('role', 'student')->count(),
        'lecturersCount'  => User::where('role', 'lecturer')->count(),
        'departmentsCount'=> Department::count(),
        'coursesCount'    => Course::count(),
        'lecturesCount'   => Lecture::count(),
        'liveLecturesCount' => Lecture::where('status', 'live')->count(),
        'studentsByDepartment' => $studentsByDepartment,
    ]);
}
}