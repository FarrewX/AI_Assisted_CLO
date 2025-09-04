<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Role::insert([
            ['role_id' => '0001', 'role_name' => 'Admin'],
            ['role_id' => '0002', 'role_name' => 'User'],
            ['role_id' => '0003', 'role_name' => 'Manager'],
        ]);
    }
}
