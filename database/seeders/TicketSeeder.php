<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Ticket;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();
        if ($events->isEmpty()) {
            $events = Event::factory()->count(5)->create();
        }

        foreach ($events as $event) {
            Ticket::factory()->count(3)->create([
                'event_id' => $event->id,
            ]);
        }
    }
}
