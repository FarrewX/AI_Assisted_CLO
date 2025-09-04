<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;

class StatusFactory extends Factory
{
    protected $model = Status::class;

    public function definition()
    {
        $course = Course::inRandomOrder()->first();
        $course_id = $course->course_id ?? '0000';

        return [
            'course_id' => $course_id,
            'startprompt' => $this->faker->dateTimeThisMonth(),
            'generated' => $this->faker->dateTimeThisMonth(),
            'downloaded' => $this->faker->dateTimeThisMonth(),
            'success' => $this->faker->dateTimeThisMonth(),
        ];
    }
}
