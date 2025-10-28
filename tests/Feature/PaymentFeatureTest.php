<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;

class PaymentFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_pay_for_booking_and_booking_is_confirmed(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customer);

        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'price' => 40,
            'quantity' => 100,
        ]);

        $booking = Booking::create([
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'quantity' => 3,
            'status' => 'pending',
        ]);

        // Make PaymentService deterministic
        $this->partialMock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('processPayment')
                ->once()
                ->withArgs(fn ($amount) => $amount > 0)
                ->andReturn([
                    'status' => 'success',
                    'transaction_id' => 'TEST_TXN_123',
                    'amount' => 120.00,
                ]);
        });

        $res = $this->postJson("/api/bookings/{$booking->id}/payment");

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Payment processed successfully',
            ]);

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    }
}
