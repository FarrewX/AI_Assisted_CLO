<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrInsert(
            ['user_id' => 'unknown'],
            [
                'role_id' => '1',
                'name' => 'Unknown User',
                'email' => 'unknown@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}