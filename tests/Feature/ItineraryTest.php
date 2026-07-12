<?php

namespace Tests\Feature;

use App\Models\ItineraryItem;
use App\Models\Trip;
use App\Models\TripMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItineraryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Trip $trip;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->trip = Trip::factory()->create(['host_id' => $this->user->id]);
        TripMember::create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'role' => 'host',
            'joined_at' => now(),
        ]);
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_user_can_add_itinerary_item(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson("/api/trips/{$this->trip->id}/itinerary", [
                'title' => 'Pura Tanah Lot',
                'description' => 'Kunjungan ke pura laut',
                'start_time' => '08:00',
                'end_time' => '11:00',
                'location' => 'Tabanan, Bali',
                'estimated_budget' => 100000,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);
    }

    public function test_user_can_reorder_itinerary(): void
    {
        $item1 = ItineraryItem::factory()->create([
            'trip_id' => $this->trip->id,
            'title' => 'Item 1',
            'start_time' => '08:00',
            'end_time' => '09:00',
            'order_index' => 0,
        ]);

        $item2 = ItineraryItem::factory()->create([
            'trip_id' => $this->trip->id,
            'title' => 'Item 2',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'order_index' => 1,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/trips/{$this->trip->id}/itinerary/reorder", [
                'items' => [$item2->id, $item1->id],
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_user_can_delete_itinerary_item(): void
    {
        $item = ItineraryItem::factory()->create([
            'trip_id' => $this->trip->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson("/api/itinerary/{$item->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_itinerary_items_ordered_by_order_index(): void
    {
        ItineraryItem::factory()->create([
            'trip_id' => $this->trip->id,
            'title' => 'Third',
            'order_index' => 2,
        ]);
        ItineraryItem::factory()->create([
            'trip_id' => $this->trip->id,
            'title' => 'First',
            'order_index' => 0,
        ]);
        ItineraryItem::factory()->create([
            'trip_id' => $this->trip->id,
            'title' => 'Second',
            'order_index' => 1,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/trips/{$this->trip->id}/itinerary");

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('First', $data[0]['title']);
        $this->assertEquals('Second', $data[1]['title']);
        $this->assertEquals('Third', $data[2]['title']);
    }
}
