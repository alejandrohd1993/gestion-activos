<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::create([
            'name' => 'horas',
            'type' => 'uso_acumulado',
        ]);

        Unit::create([
            'name' => 'kilometros',
            'type' => 'uso_acumulado',
        ]);

        Unit::create([
            'name' => 'dias',
            'type' => 'calendario',
        ]);

    }
}
