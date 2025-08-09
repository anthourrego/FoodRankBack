<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Country::create([
            'name' => 'Colombia',
            'code' => 'CO',
            'is_active' => true,
            'created_by' => 1, // Assuming a user with ID 1 exists
            'updated_by' => 1, // Assuming a user with ID 1 exists
        ]);
    }
}
