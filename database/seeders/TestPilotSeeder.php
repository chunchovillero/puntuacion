<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pilot;
use App\Models\Club;

class TestPilotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener el primer club
        $club = Club::first();
        
        if (!$club) {
            $this->command->error('No hay clubes disponibles. Crea uno primero.');
            return;
        }

        // Crear pilotos de prueba
        $pilots = [
            [
                'first_name' => 'Juan',
                'last_name' => 'Perez',
                'rut' => '12345678-9',
                'nickname' => 'Juanito',
                'age' => 25,
                'birth_date' => '1999-01-15',
                'phone' => '+56912345678',
                'email' => 'juan.perez@test.com',
                'club_id' => $club->id,
                'ranking_points' => 1500,
                'status' => 'active',
                'joined_club_date' => now()->subMonths(6),
                'photo' => 'pilots/user1-128x128.jpg', // Foto de prueba
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Gonzalez',
                'rut' => '98765432-1',
                'nickname' => 'Maru',
                'age' => 22,
                'birth_date' => '2002-03-20',
                'phone' => '+56987654321',
                'email' => 'maria.gonzalez@test.com',
                'club_id' => $club->id,
                'ranking_points' => 1200,
                'status' => 'active',
                'joined_club_date' => now()->subMonths(4),
                'photo' => 'pilots/user2-160x160.jpg', // Foto de prueba
            ],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Silva',
                'rut' => '15975348-6',
                'nickname' => 'Carlitos',
                'age' => 19,
                'birth_date' => '2005-07-10',
                'phone' => '+56915975348',
                'email' => 'carlos.silva@test.com',
                'club_id' => $club->id,
                'ranking_points' => 800,
                'status' => 'active',
                'joined_club_date' => now()->subMonths(2),
                // Sin foto para probar el avatar por defecto
            ],
        ];

        foreach ($pilots as $pilotData) {
            // Verificar si el piloto ya existe por RUT
            $existingPilot = Pilot::where('rut', $pilotData['rut'])->first();
            
            if (!$existingPilot) {
                $pilot = Pilot::create($pilotData);
                $this->command->info('Piloto creado: ' . $pilot->full_name . ' (' . $pilot->rut . ')');
            } else {
                $this->command->info('Piloto ya existe: ' . $existingPilot->full_name . ' (' . $existingPilot->rut . ')');
            }
        }

        $this->command->info('Pilotos de prueba configurados exitosamente!');
    }
}
