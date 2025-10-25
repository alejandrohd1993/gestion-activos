<?php

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Expense::create([
            'name' => 'Gastos Varios',
            'is_active' => true,
        ]);
        Expense::create([
            'name' => 'Combustible',
            'is_active' => true,
        ]);
        Expense::create([
            'name' => 'Aceite',
            'is_active' => true,
        ]);
        Expense::create([
            'name' => 'Viáticos',
            'is_active' => true,
        ]);
    }
}
