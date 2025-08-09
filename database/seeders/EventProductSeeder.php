<?php

namespace Database\Seeders;

use App\Models\EventProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asociamos el producto "Maridaje de Vinos" (ID 2) al evento "Noche de Vinos y Jazz" (ID 1)
        EventProduct::create([
            'event_id' => 1,
            'product_id' => 2,
            'created_by' => null,
            'updated_by' => null,
        ]);

        // Podríamos asociar más productos al mismo evento si quisiéramos
        // EventProduct::create([
        //     'event_id' => 1,
        //     'product_id' => 1, // Por ejemplo, el Menú Degustación
        //     'created_by' => 1,
        //     'updated_by' => 1,
        // ]);
    }
}
