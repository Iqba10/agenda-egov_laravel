<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@agenda-egov.local'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@agenda-egov.local'],
            [
                'name' => 'User Biasa',
                'username' => 'user',
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]
        );
    }
}
