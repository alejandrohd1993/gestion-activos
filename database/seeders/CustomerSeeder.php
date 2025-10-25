<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Customer::create([
            'nit' => '222222222222',
            'name' => 'Consumidor final',
            'email' => 'colaboradoresrv@gmail.com',
            'phone' => '3000000002',
            'address' => 'Calle 123 #45-67, Ciudad',
            'person_type' => 'juridica',
        ]);
    }
}
