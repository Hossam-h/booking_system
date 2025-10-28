<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizers = User::query()->where('role', 'organizer')->get();
        if ($organizers->isEmpty()) {
            $organizers = User::factory(3)->create(['role' => 'organizer']);
        }

        foreach ($organizers as $organizer) {
            Event::factory()->count(3)->create([
                'created_by' => $organizer->id,
            ]);
        }
    }
}
