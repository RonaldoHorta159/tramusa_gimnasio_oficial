<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\LockerSeeder;  // ⚠️ Importa aquí tu seeder

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Otros seeders que quieras ejecutar...
        $this->call([
            LockerSeeder::class,
            UserSeeder::class,
        ]);
    }
}