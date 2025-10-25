<?php

namespace Database\Seeders;

use App\Models\Component;
use App\Models\Unit;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(ProviderSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(ComponentSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(ExpenseSeeder::class);
    }
}
