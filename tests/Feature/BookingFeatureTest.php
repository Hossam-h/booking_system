<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;

class BookingFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_book_a_ticket(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customer);

        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'price' => 50,
            'quantity' => 100,
        ]);

        $payload = [
            'quantity' => 2,
        ];

        $res = $this->postJson("/api/tickets/{$ticket->id}/bookings", $payload);

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Booking created successfully',
            ])
            ->assertJsonPath('data.ticket.id', $ticket->id);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'quantity' => 2,
            'status' => 'pending',
        ]);
    }

    public function test_booking_fails_when_not_enough_tickets(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customer);

        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'price' => 25,
            'quantity' => 1,
        ]);

        $payload = [
            'quantity' => 5,
        ];

        $res = $this->postJson("/api/tickets/{$ticket->id}/bookings", $payload);

        $res->assertStatus(422);
    }
}
