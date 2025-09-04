<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition()
    {
        // เลือก user_id จากผู้ใช้ที่มีอยู่
        $user_id = User::inRandomOrder()->first()->user_id ?? Str::uuid();

        return [
            'course_id' => (string) Str::uuid(),
            'user_id' => $user_id,
            'course_detail' => $this->faker->sentence(6),
        ];
    }
}
