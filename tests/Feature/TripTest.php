<?php

namespace Tests\Feature;

use App\Models\Trip;
use App\Models\TripMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_user_can_create_trip(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/trips', [
                'name' => 'Bali Trip',
                'destination' => 'Bali',
                'start_date' => '2026-08-01',
                'end_date' => '2026-08-05',
                'total_budget' => 5000000,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'share_code'],
            ]);

        $this->assertDatabaseHas('trip_members', [
            'user_id' => $this->user->id,
            'role' => 'host',
        ]);
    }

    public function test_user_can_join_trip_with_share_code(): void
    {
        $host = User::factory()->create();
        $trip = Trip::factory()->create([
            'host_id' => $host->id,
            'share_code' => '123456',
        ]);

        TripMember::create([
            'trip_id' => $trip->id,
            'user_id' => $host->id,
            'role' => 'host',
            'joined_at' => now(),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/trips/join', [
                'share_code' => '123456',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_user_can_get_trips(): void
    {
        $trip = Trip::factory()->create(['host_id' => $this->user->id]);
        TripMember::create([
            'trip_id' => $trip->id,
            'user_id' => $this->user->id,
            'role' => 'host',
            'joined_at' => now(),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/trips');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_non_member_cannot_access_trip(): void
    {
        $otherUser = User::factory()->create();
        $trip = Trip::factory()->create(['host_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/trips/' . $trip->id);

        $response->assertStatus(403);
    }
}
