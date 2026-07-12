<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\TripMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TripMemberFactory extends Factory
{
    protected $model = TripMember::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'trip_id' => Trip::factory(),
            'user_id' => User::factory(),
            'role' => fake()->randomElement(['host', 'member']),
            'joined_at' => now(),
        ];
    }
}
