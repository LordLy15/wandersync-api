<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'host_id' => User::factory(),
            'name' => fake()->sentence(3),
            'destination' => fake()->city(),
            'start_date' => fake()->dateTimeBetween('now', '+1 month'),
            'end_date' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'total_budget' => fake()->numberBetween(1000000, 50000000),
            'share_code' => str_pad(fake()->numberBetween(0, 999999), 6, '0', STR_PAD_LEFT),
        ];
    }
}
