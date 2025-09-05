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
            'course_id' => str_pad((string) fake()->numberBetween(0, 999999), 6, '0', STR_PAD_LEFT),
            'user_id' => $user_id,
            'course_detail' => $this->faker->sentence(6),
        ];
    }
}
