<?php

use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\LecturerController;
use App\Http\Controllers\Admin\CourseOfferingController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\LectureScheduleController;
use App\Http\Controllers\Lecturer\DashboardController as LecturerDashboardController;
use App\Http\Controllers\Lecturer\LectureController as LecturerLectureController;
use App\Http\Controllers\Lecturer\StreamController as LecturerStreamController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\LectureController as StudentLectureController;
use App\Http\Controllers\Student\StreamController as StudentStreamController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('departments', DepartmentController::class);

        Route::resource('semesters', SemesterController::class)
            ->only(['index']);

        Route::resource('courses', CourseController::class);

        Route::resource('course-offerings', CourseOfferingController::class)
            ->middleware('role:admin');

        Route::resource('lecturers', LecturerController::class)
            ->middleware('role:admin');
        Route::resource('students', StudentController::class)
            ->middleware('role:admin');
      
        Route::resource( 'lecture-schedules', LectureScheduleController::class );

    });

// لوحة المحاضر: محمية بدور lecturer
Route::middleware(['auth', 'role:lecturer'])
    ->prefix('lecturer')
    ->name('lecturer.')
    ->group(function () {

        Route::get('/dashboard', [LecturerDashboardController::class, 'index'])
            ->name('dashboard');

        // إدارة محاضرات مقرر معين
        Route::get('/course-offerings/{courseOffering}/lectures', [LecturerLectureController::class, 'index'])
            ->name('lectures.index');

        Route::get('/course-offerings/{courseOffering}/lectures/create', [LecturerLectureController::class, 'create'])
            ->name('lectures.create');

        Route::post('/course-offerings/{courseOffering}/lectures', [LecturerLectureController::class, 'store'])
            ->name('lectures.store');

        Route::delete('/lectures/{lecture}', [LecturerLectureController::class, 'destroy'])
            ->name('lectures.destroy');

        Route::delete('/lecture-files/{lectureFile}', [LecturerLectureController::class, 'destroyFile'])
            ->name('lecture-files.destroy');

        // البث المباشر: بدء البث من موعد اليوم، صفحة البث، إنهاء البث
        Route::post('/lecture-schedules/{lectureSchedule}/stream/start', [LecturerStreamController::class, 'start'])
            ->name('stream.start');

        // بث طارئ لأي مقرر يدرّسه المحاضر، بدون الحاجة لموعد مطابق لليوم الحالي
        Route::post('/course-offerings/{courseOffering}/stream/emergency', [LecturerStreamController::class, 'startEmergency'])
            ->name('stream.emergency');

        Route::get('/lectures/{lecture}/stream', [LecturerStreamController::class, 'show'])
            ->name('stream.show');

        Route::post('/lectures/{lecture}/stream/end', [LecturerStreamController::class, 'end'])
            ->name('stream.end');

    });

// لوحة الطالب: محمية بدور student
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::get('/dashboard', [StudentDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/lectures/{lecture}', [StudentLectureController::class, 'show'])
            ->name('lectures.show');

        // مشاهدة البث المباشر
        Route::get('/lectures/{lecture}/watch', [StudentStreamController::class, 'show'])
            ->name('stream.watch');

    });

// شات المقرر: مشترك بين المحاضر وطلاب المقرر، لذلك التحقق من الصلاحية كامل داخل ChatController
Route::middleware('auth')
    ->prefix('course-offerings/{courseOffering}/chat')
    ->name('chat.')
    ->group(function () {

        Route::get('/', [ChatController::class, 'index'])
            ->name('show');

        Route::get('/messages', [ChatController::class, 'fetch'])
            ->name('fetch');

        Route::post('/messages', [ChatController::class, 'store'])
            ->name('store');

    });

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    

});

require __DIR__.'/auth.php';