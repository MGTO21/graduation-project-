<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
    [
        'email' => 'admin@university.com',
    ],
    [
        'role'          => 'admin',
        'university_id' => 'ADMIN001',
        'name'          => 'مدير النظام',
        'phone'         => '0500000000',
        'department_id' => null,
        'semester_id'   => null,
        'profile_image' => null,
        'is_active'     => true,
        'password'      => Hash::make('Admin@123'),
    ]
);
    }
}