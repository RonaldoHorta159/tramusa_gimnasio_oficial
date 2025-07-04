<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@tramusagimnasio.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin'), // Cambia la contraseña aquí
            'remember_token' => Str::random(10),
        ]);
    }
}
