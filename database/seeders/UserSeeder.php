<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario administrativo
        User::create([
            'name' => 'Admin',
            'email' => 'anthonyjdiaz89@gmail.com',
            'password' => Hash::make('admin123'),
            'type' => 'administrativo',
            'phone' => '3000000000',
            'is_active' => true,
        ]);

        // Usuario operativo
        User::create([
            'name' => 'Operador',
            'email' => 'operador@example.com',
            'password' => Hash::make('operador123'),
            'type' => 'operativo',
            'phone' => '3000000001',
            'is_active' => true,
        ]);
    }
}
