<?php

namespace Database\Factories;

use App\Models\ItineraryItem;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ItineraryItemFactory extends Factory
{
    protected $model = ItineraryItem::class;

    public function definition(): array
    {
        $startHour = fake()->numberBetween(6, 20);
        return [
            'id' => Str::uuid()->toString(),
            'trip_id' => Trip::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'start_time' => sprintf('%02d:00', $startHour),
            'end_time' => sprintf('%02d:00', $startHour + fake()->numberBetween(1, 4)),
            'location' => fake()->address(),
            'estimated_budget' => fake()->numberBetween(50000, 500000),
            'order_index' => fake()->numberBetween(0, 10),
        ];
    }
}
