<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\RestaurantProduct;
use Illuminate\Database\Seeder;

class RestaurantProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RestaurantProduct::create([
            'name' => 'Menú Degustación "El Viaje"',
            'description' => 'Un recorrido por los sabores de Colombia en 12 pasos.',
            'image_url' => 'https://elcielorestaurant.com/images/menu_viaje.jpg',
            'restaurant_id' => 1, // Asignado al restaurante "El Cielo" (ID 1)
            'created_by' => null,
            'updated_by' => null,
        ]);

        RestaurantProduct::create([
            'name' => 'Maridaje de Vinos',
            'description' => 'Selección de vinos para acompañar el menú degustación.',
            'image_url' => 'https://elcielorestaurant.com/images/maridaje.jpg',
            'restaurant_id' => 1, // Asignado también a "El Cielo"
            'created_by' => null,
            'updated_by' => null,
        ]);
    }
}
