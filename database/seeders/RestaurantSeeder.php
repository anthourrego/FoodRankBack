<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Restaurant::create([
            'name' => 'El Cielo',
            'description' => 'Cocina creativa de autor.',
            'address' => 'Cra. 40 #10a-22, El Poblado, Medellín',
            'email' => 'reservas@elcielorestaurant.com',
            'phone' => '3113162712',
            'website' => 'https://elcielorestaurant.com/',
            'city_id' => 1, // Assuming a city with ID 1 exists
            'created_by' => null, // Assuming a user with ID 1 exists
            'updated_by' => null, // Assuming a user with ID 1 exists
        ]);
    }
}
