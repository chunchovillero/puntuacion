<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matchday;
use App\Models\Championship;

class MatchdaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Verificar que existe el campeonato
        $championship = Championship::find(2);
        if (!$championship) {
            $this->command->error('No se encontró el campeonato con ID 2. Asegúrate de ejecutar ChampionshipSeeder primero.');
            return;
        }

        // Limpiar tabla antes de insertar (opcional - comentado por seguridad)
        // Matchday::truncate();

        $matchdays = [
            [
                'name' => 'Jornada 1 - Marzo 2025',
                'championship_id' => 2,
                'date' => '2025-03-06',
                'start_time' => null,
                'end_time' => null,
                'location' => 'Pista BMX Metropolitana',
                'description' => 'Primera jornada del Campeonato Metropolitano BMX 2025',
                'max_participants' => 150,
                'registration_deadline' => '2025-03-04 23:59:59',
                'registration_fee' => 5000,
                'status' => 'completed',
                'weather_conditions' => 'Soleado',
                'track_conditions' => 'Buenas',
            ],
            [
                'name' => 'Jornada 2 - Abril 2025',
                'championship_id' => 2,
                'date' => '2025-04-01',
                'start_time' => '09:15:00',
                'end_time' => null,
                'location' => 'Pista BMX Metropolitana',
                'description' => 'Segunda jornada del Campeonato Metropolitano BMX 2025',
                'max_participants' => 150,
                'registration_deadline' => '2025-03-30 23:59:59',
                'registration_fee' => 5000,
                'status' => 'completed',
                'weather_conditions' => null,
                'track_conditions' => null,
            ],
            [
                'name' => 'Jornada 3 - Mayo 2025',
                'championship_id' => 2,
                'date' => '2025-05-17',
                'start_time' => null,
                'end_time' => null,
                'location' => 'Pista BMX Metropolitana',
                'description' => 'Tercera jornada del Campeonato Metropolitano BMX 2025',
                'max_participants' => 150,
                'registration_deadline' => '2025-05-15 23:59:59',
                'registration_fee' => 5000,
                'status' => 'completed',
                'weather_conditions' => null,
                'track_conditions' => null,
            ],
            [
                'name' => 'Jornada 4 - Julio 2025',
                'championship_id' => 2,
                'date' => '2025-07-12',
                'start_time' => '09:00:00',
                'end_time' => null,
                'location' => 'Pista BMX Metropolitana',
                'description' => 'Cuarta jornada del Campeonato Metropolitano BMX 2025',
                'max_participants' => 150,
                'registration_deadline' => '2025-07-10 23:59:59',
                'registration_fee' => 5000,
                'status' => 'scheduled',
                'weather_conditions' => null,
                'track_conditions' => null,
            ],
            [
                'name' => 'Jornada 5 - Julio 2025',
                'championship_id' => 2,
                'date' => '2025-07-30',
                'start_time' => '09:18:00',
                'end_time' => null,
                'location' => 'Pista BMX Metropolitana',
                'description' => 'Quinta jornada del Campeonato Metropolitano BMX 2025',
                'max_participants' => 150,
                'registration_deadline' => '2025-07-28 23:59:59',
                'registration_fee' => 5000,
                'status' => 'completed',
                'weather_conditions' => null,
                'track_conditions' => null,
            ],
        ];

        foreach ($matchdays as $matchday) {
            // Verificar si ya existe una jornada con la misma fecha
            $existing = Matchday::where('championship_id', $matchday['championship_id'])
                              ->where('date', $matchday['date'])
                              ->first();
            
            if (!$existing) {
                Matchday::create($matchday);
                $this->command->info("Jornada creada: {$matchday['name']} - {$matchday['date']}");
            } else {
                $this->command->warn("Jornada ya existe: {$matchday['name']} - {$matchday['date']}");
            }
        }

        $this->command->info('MatchdaySeeder ejecutado exitosamente. Total jornadas procesadas: ' . count($matchdays));
    }
}
