<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Booking;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::query()->where('role', 'customer')->get();
        if ($customers->isEmpty()) {
            $customers = User::factory(10)->create(['role' => 'customer']);
        }

        $tickets = Ticket::all();
        if ($tickets->isEmpty()) {
            $tickets = Ticket::factory()->count(10)->create();
        }

        foreach ($customers as $customer) {
            $sample = $tickets->random(min(3, $tickets->count()));
            foreach ($sample as $ticket) {
                Booking::factory()->create([
                    'user_id' => $customer->id,
                    'ticket_id' => $ticket->id,
                ]);
            }
        }
    }
}
