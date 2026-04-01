<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_SEED_EMAIL', 'admin@example.com.au')],
            [
                'name'     => 'FCPR Admin',
                'password' => Hash::make(env('ADMIN_SEED_PASSWORD', 'ChangeMe123!')),
                'is_admin' => true,
                'is_active' => true,
            ]
        );
    }
}
