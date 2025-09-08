<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Prompt;
use App\Models\Status;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Roles user
        $this->call([
            RoleSeeder::class
        ]);

        // Users
        // User::factory()->count(10)->create();

        // Courses
        // Course::factory()->count(10)->create();

        // Prompts
        // Prompt::factory()->count(20)->create();

        // Status
        // Status::factory()->count(10)->create();
    }
}
