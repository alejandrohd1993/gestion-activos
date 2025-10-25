<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Colaboradores RV
        Provider::create([
            'nit' => '222222222222',
            'name' => 'Colaboradores RV',
            'email' => 'colaboradoresrv@gmail.com',
            'phone' => '3000000002',
            'address' => 'Calle 123 #45-67, Ciudad',
            'person_type' => 'juridica',
        ]);
    }
}
