<?php

namespace Database\Seeders;

use App\Models\EventProductBranch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventProductBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // El producto "Maridaje de Vinos" del evento "Noche de Jazz" estará disponible en la sucursal de El Poblado.
        EventProductBranch::create([
            'event_product_id' => 1,     // Asumiendo que el id de la relación evento-producto es 1
            'restaurant_branch_id' => 1, // Asumiendo que el id de la sucursal de El Poblado es 1
            'created_by' => null,
            'updated_by' => null,
        ]);
    }
}
