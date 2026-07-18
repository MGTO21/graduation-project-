<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Lecture;
use App\Models\LectureFile;
use App\Models\LectureSchedule;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/*
 * بيانات تجريبية عشان أي حد يشغّل المشروع يقدر يجرب كل الميزات فوراً بدون ما يدخل
 * بيانات يدوياً: قسم، مقرر، محاضر وطالب تجريبيين، طرح المقرر، موعدان في الجدول
 * الأسبوعي، ومحاضرة برفع ملف PDF تجريبي. القيم مطابقة لجدول الحسابات التجريبية
 * الموثّق في README.md وTASHGHEEL.md.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::firstOrCreate(
            ['code' => 'IT'],
            ['name' => 'تقانة المعلومات', 'is_active' => true]
        );

        $semester1 = Semester::where('number', 1)->first();

        $lecturer = User::firstOrCreate(
            ['university_id' => 'LEC001'],
            [
                'role'          => 'lecturer',
                'name'          => 'د. أحمد محمد',
                'email'         => 'lec001@university.com',
                'phone'         => '0511111111',
                'department_id' => $department->id,
                'semester_id'   => null,
                'is_active'     => true,
                'password'      => Hash::make('Lecturer@123'),
            ]
        );

        $student = User::firstOrCreate(
            ['university_id' => 'STU001'],
            [
                'role'          => 'student',
                'name'          => 'محمد عبدالله',
                'email'         => 'stu001@university.com',
                'phone'         => '0522222222',
                'department_id' => $department->id,
                'semester_id'   => $semester1->id,
                'is_active'     => true,
                'password'      => Hash::make('Student@123'),
            ]
        );

        $course = Course::firstOrCreate(
            ['code' => 'CS101'],
            [
                'name'          => 'مقدمة في البرمجة',
                'credit_hours'  => 3,
                'is_active'     => true,
            ]
        );

        $offering = CourseOffering::firstOrCreate(
            [
                'course_id'     => $course->id,
                'department_id' => $department->id,
                'semester_id'   => $semester1->id,
                'lecturer_id'   => $lecturer->id,
            ],
            ['is_active' => true]
        );

        $saturday = LectureSchedule::firstOrCreate([
            'course_offering_id' => $offering->id,
            'day'                => 'Saturday',
            'start_time'         => '08:00:00',
            'end_time'           => '10:00:00',
        ], ['is_active' => true]);

        LectureSchedule::firstOrCreate([
            'course_offering_id' => $offering->id,
            'day'                => 'Sunday',
            'start_time'         => '10:00:00',
            'end_time'           => '12:00:00',
        ], ['is_active' => true]);

        $lecture = Lecture::firstOrCreate([
            'lecture_schedule_id' => $saturday->id,
            'lecture_date'        => now()->toDateString(),
        ], ['status' => 'scheduled']);

        // ملف PDF تجريبي بسيط لمجلد التخزين - بينعمل مرة واحدة بس لو مش موجود أصلاً
        $storedName = 'test_lecture.pdf';

        if (! Storage::disk('public')->exists('lectures/' . $storedName)) {
            Storage::disk('public')->put(
                'lectures/' . $storedName,
                "%PDF-1.4\n%demo file for graduation project testing\n"
            );
        }

        LectureFile::firstOrCreate([
            'lecture_id' => $lecture->id,
            'stored_name' => $storedName,
        ], [
            'original_name' => 'محاضرة تجريبية.pdf',
            'file_path'     => 'lectures/' . $storedName,
            'file_type'     => 'pdf',
            'file_size'     => Storage::disk('public')->size('lectures/' . $storedName),
        ]);
    }
}
