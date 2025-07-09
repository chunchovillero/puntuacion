<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Championship;
use App\Models\Matchday;

class TestMatchdaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener el primer campeonato
        $championship = Championship::first();
        
        if (!$championship) {
            $this->command->error('No hay campeonatos disponibles. Crea uno primero.');
            return;
        }

        // Actualizar jornada existente si la hay
        $existingMatchday = Matchday::first();
        if ($existingMatchday) {
            $existingMatchday->update([
                'public_registration_enabled' => true,
                'status' => 'scheduled',
                'date' => '2025-07-15',
                'entry_fee' => 5000,
                'registration_start_date' => now(),
                'registration_end_date' => now()->addDays(10),
            ]);
            $this->command->info('Jornada existente actualizada: ' . $existingMatchday->full_name);
        }

        // Crear jornadas de prueba adicionales
        $matchdays = [
            [
                'championship_id' => $championship->id,
                'number' => 2,
                'name' => 'Jornada de Verano BMX',
                'date' => '2025-07-20',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'venue' => 'Pista BMX Maipú',
                'address' => 'Av. Américo Vespucio 1501, Maipú',
                'organizer_name' => 'Club BMX Maipú',
                'organizer_contact' => 'contacto@bmxmaipu.cl',
                'organizer_phone' => '+56 9 8765 4321',
                'description' => 'Segunda jornada del campeonato metropolitano con registro público habilitado.',
                'registration_start_date' => now(),
                'registration_end_date' => now()->addDays(15),
                'public_registration_enabled' => true,
                'entry_fee' => 5000,
                'status' => 'scheduled',
            ],
            [
                'championship_id' => $championship->id,
                'number' => 3,
                'name' => 'Jornada BMX Las Condes',
                'date' => '2025-08-10',
                'start_time' => '08:30:00',
                'end_time' => '16:00:00',
                'venue' => 'Parque Araucano',
                'address' => 'Av. Presidente Riesco 5877, Las Condes',
                'organizer_name' => 'BMX Las Condes',
                'organizer_contact' => 'info@bmxlascondes.cl',
                'organizer_phone' => '+56 9 1111 2222',
                'description' => 'Tercera jornada en el parque Araucano con todas las categorías.',
                'registration_start_date' => now()->addDays(2),
                'registration_end_date' => now()->addDays(20),
                'public_registration_enabled' => true,
                'entry_fee' => 6000,
                'status' => 'scheduled',
            ],
        ];

        foreach ($matchdays as $matchdayData) {
            $matchday = Matchday::create($matchdayData);
            $this->command->info('Jornada creada: ' . $matchday->full_name);
        }

        $this->command->info('Jornadas de prueba configuradas exitosamente!');
    }
}
