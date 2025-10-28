<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Event;

class EventFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_create_event(): void
    {
        $organizer = User::factory()->create([
            'role' => 'organizer',
        ]);

        Sanctum::actingAs($organizer);

        $payload = [
            'title' => 'Tech Conference',
            'description' => 'Annual tech event',
            'date' => now()->addDays(10)->toDateString(),
            'location' => 'New York',
        ];

        $res = $this->postJson('/api/events', $payload);

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Event created successfully',
            ])
            ->assertJsonPath('data.title', 'Tech Conference');

        $this->assertDatabaseHas('events', [
            'title' => 'Tech Conference',
            'created_by' => $organizer->id,
        ]);
    }

    public function test_non_organizer_cannot_create_event(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customer);

        $payload = [
            'title' => 'Unauthorized Event',
            'date' => now()->addDays(5)->toDateString(),
            'location' => 'LA',
        ];

        $res = $this->postJson('/api/events', $payload);
        $res->assertStatus(403);
    }
}
