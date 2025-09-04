<?php

namespace Database\Factories;

use App\Models\Prompt;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\User;

class PromptFactory extends Factory
{
    protected $model = Prompt::class;

    public function definition()
    {
        $course = Course::inRandomOrder()->first();
        $user_id = $course->user_id ?? Str::uuid();
        $course_id = $course->course_id ?? Str::uuid();

        return [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'name' => $this->faker->words(3, true),
            'course_text' => $this->faker->paragraph(),
        ];
    }
}
