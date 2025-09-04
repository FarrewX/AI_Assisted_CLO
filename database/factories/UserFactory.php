<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'user_id' => (string) Str::uuid(), // เพราะ primary key เป็น string
            'role_id' => $this->faker->randomElement(['0001','0002','0003']), // เลือก role_id ที่มีอยู่จริง
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}