<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {

            Semester::firstOrCreate(
                ['number' => $i],
                [
                    'academic_year' => ceil($i / 2),
                    'name' => "Semester $i",
                    'is_active' => true,
                ]
            );

        }
    }
}