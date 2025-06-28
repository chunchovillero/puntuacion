<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear usuario administrador
        User::firstOrCreate(
            ['email' => 'admin@bmx.com'],
            [
                'name' => 'Administrador BMX',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Crear usuario de prueba
        User::firstOrCreate(
            ['email' => 'demo@bmx.com'],
            [
                'name' => 'Usuario Demo',
                'password' => Hash::make('demo123'),
                'email_verified_at' => now(),
            ]
        );

        echo "Usuarios creados:\n";
        echo "- Administrador: admin@bmx.com / password123\n";
        echo "- Demo: demo@bmx.com / demo123\n";
    }
}
