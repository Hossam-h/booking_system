<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = Booking::with('ticket')->get();
        if ($bookings->isEmpty()) {
            return;
        }

        foreach ($bookings as $booking) {
            $amount = ($booking->ticket?->price ?? 0) * $booking->quantity;
            Payment::factory()->create([
                'booking_id' => $booking->id,
                'amount' => $amount,
            ]);
        }
    }
}
