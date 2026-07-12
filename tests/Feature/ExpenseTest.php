<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Trip;
use App\Models\TripMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
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

    public function test_user_can_add_expense(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson("/api/trips/{$this->trip->id}/expenses", [
                'amount' => 150000,
                'category' => 'food',
                'description' => 'Makan malam',
                'date' => '2026-08-01',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);
    }

    public function test_user_can_get_expense_summary(): void
    {
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'amount' => 500000,
            'category' => 'transport',
            'date' => '2026-08-01',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/trips/{$this->trip->id}/expenses/summary");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['total_budget', 'total_spent', 'remaining', 'status'],
            ]);
    }

    public function test_user_can_delete_own_expense(): void
    {
        $expense = Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_expense_validation_requires_valid_category(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson("/api/trips/{$this->trip->id}/expenses", [
                'amount' => 150000,
                'category' => 'invalid_category',
                'date' => '2026-08-01',
            ]);

        $response->assertStatus(422);
    }
}
