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
            'user_id' => str_pad((string) fake()->numberBetween(0, 9999999999), 10, '0', STR_PAD_LEFT),
            'role_id' => $this->faker->randomElement(['1','2']), // เลือก role_id ที่มีอยู่จริง
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}