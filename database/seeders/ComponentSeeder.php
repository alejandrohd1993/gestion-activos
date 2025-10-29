<?php

namespace Database\Seeders;

use App\Models\Component;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Component::create([
            'name' => 'Aceite',
            'category' => 'generador',
            'unit_id' => 1, 
            'duration' => 720000,
        ]);

        Component::create([
            'name' => 'Filtro ACPM',
            'category' => 'generador',
            'unit_id' => 1, 
            'duration' => 720000,
        ]);

        Component::create([
            'name' => 'SOAT',
            'category' => 'vehiculo',
            'unit_id' => 3, 
            'duration' => 365,
        ]);

        Component::create([
            'name' => 'Filtro ACPM',
            'category' => 'vehiculo',
            'unit_id' => 2, 
            'duration' => 200,
        ]);
    }
}
