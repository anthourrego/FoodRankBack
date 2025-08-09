<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\RestaurantBranch;
use Illuminate\Database\Seeder;

class RestaurantBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sede de El Poblado
        RestaurantBranch::create([
            'address' => 'Cra. 40 #10a-22, El Poblado, Medellín',
            'phone' => '3113162712',
            'latitude' => '6.2094',
            'longitude' => '-75.5691',
            'city_id' => 1,       // Asumiendo que la ciudad con ID 1 es Medellín
            'restaurant_id' => 1, // Asumiendo que el restaurante "El Cielo" tiene ID 1
            'created_by' => null,
            'updated_by' => null,
        ]);

        // Sede de Laureles
        RestaurantBranch::create([
            'address' => 'Av. 74B #39-46, Laureles, Medellín',
            'phone' => '3113162713',
            'latitude' => '6.2447',
            'longitude' => '-75.5919',
            'city_id' => 1,
            'restaurant_id' => 1, // También pertenece al restaurante "El Cielo"
            'created_by' => null,
            'updated_by' => null,
        ]);
    }
}
