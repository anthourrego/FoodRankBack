<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::create([
            'name' => 'Carnaval Burger 2025',
            'description' => '',
            'start_date' => now()->addDays(1)->setHour(20)->setMinute(0),
            'end_date' => now()->addDays(10)->setHour(23)->setMinute(0),
            'city_id' => 1,
            'created_by' => null,
            'updated_by' => null,
        ]);
    }
}
