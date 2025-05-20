<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LockerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 75; $i++) {
            DB::table('lockers')->insert([
                'numero_locker' => $i,
                'estado_locker' => 'libre',
            ]);
        }
    }
}
