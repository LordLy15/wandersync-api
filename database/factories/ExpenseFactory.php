<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'trip_id' => Trip::factory(),
            'user_id' => User::factory(),
            'amount' => fake()->numberBetween(10000, 5000000),
            'category' => fake()->randomElement(['transport', 'accommodation', 'tickets', 'food', 'others']),
            'description' => fake()->sentence(),
            'date' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
