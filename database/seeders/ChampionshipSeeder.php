<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Championship;
use App\Models\Matchday;
use App\Models\Club;
use Carbon\Carbon;

class ChampionshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear campeonatos
        $championship2024 = Championship::create([
            'name' => 'Campeonato Metropolitano BMX',
            'year' => 2024,
            'description' => 'Campeonato oficial de BMX de la región metropolitana para el año 2024.',
            'start_date' => Carbon::createFromDate(2024, 3, 1),
            'end_date' => Carbon::createFromDate(2024, 11, 30),
            'total_matchdays' => 8,
            'status' => 'completed',
            'rules' => 'Reglas oficiales de BMX aplicables. Categorías por edad y experiencia.',
            'prizes' => 'Trofeos y medallas para los primeros 3 lugares de cada categoría.',
            'active' => true,
        ]);

        $championship2025 = Championship::create([
            'name' => 'Campeonato Metropolitano BMX',
            'year' => 2025,
            'description' => 'Campeonato oficial de BMX de la región metropolitana para el año 2025.',
            'start_date' => Carbon::createFromDate(2025, 3, 1),
            'end_date' => Carbon::createFromDate(2025, 11, 30),
            'total_matchdays' => 10,
            'status' => 'active',
            'rules' => 'Reglas oficiales de BMX aplicables. Categorías por edad y experiencia. Nuevas normativas de seguridad.',
            'prizes' => 'Trofeos, medallas y premios en efectivo para los primeros 3 lugares de cada categoría.',
            'active' => true,
        ]);

        // Obtener algunos clubes existentes
        $clubs = Club::limit(3)->get();
        $clubIds = $clubs->pluck('id')->toArray();

        // Crear jornadas para el campeonato 2024 (completado)
        for ($i = 1; $i <= 8; $i++) {
            $date = Carbon::createFromDate(2024, 3 + $i - 1, rand(1, 28));
            $organizerClubId = $i % 3 === 0 ? null : ($clubIds[($i - 1) % count($clubIds)] ?? null);
            
            Matchday::create([
                'championship_id' => $championship2024->id,
                'number' => $i,
                'date' => $date,
                'start_time' => '09:00',
                'venue' => $organizerClubId ? 'Pista Club ' . $clubs->find($organizerClubId)->name : 'Pista Central AMBMX',
                'address' => 'Dirección de la pista ' . $i,
                'organizer_club_id' => $organizerClubId,
                'status' => 'completed',
                'description' => 'Jornada ' . $i . ' del campeonato 2024. Excelente participación.',
            ]);
        }

        // Crear jornadas para el campeonato 2025 (activo)
        $statuses = ['completed', 'completed', 'completed', 'scheduled', 'scheduled', 'scheduled', 'scheduled', 'scheduled', 'scheduled', 'scheduled'];
        
        for ($i = 1; $i <= 10; $i++) {
            $date = Carbon::createFromDate(2025, 3 + ($i - 1), rand(1, 28));
            if ($i > 3) {
                $date = Carbon::createFromDate(2025, 6 + ($i - 4), rand(1, 28));
            }
            
            $organizerClubId = $i % 4 === 0 ? null : ($clubIds[($i - 1) % count($clubIds)] ?? null);
            
            Matchday::create([
                'championship_id' => $championship2025->id,
                'number' => $i,
                'date' => $date,
                'start_time' => '09:30',
                'venue' => $organizerClubId ? 'Pista ' . $clubs->find($organizerClubId)->name : 'Pista Metropolitana AMBMX',
                'address' => 'Av. Principal ' . ($i * 100) . ', Ciudad BMX',
                'organizer_club_id' => $organizerClubId,
                'status' => $statuses[$i - 1],
                'description' => $i <= 3 ? 'Jornada completada con gran éxito.' : 'Jornada programada para el campeonato 2025.',
            ]);
        }

        // Crear un campeonato adicional para 2026 (planeado)
        $championship2026 = Championship::create([
            'name' => 'Campeonato Metropolitano BMX',
            'year' => 2026,
            'description' => 'Campeonato oficial de BMX de la región metropolitana para el año 2026. En planificación.',
            'start_date' => null,
            'end_date' => null,
            'total_matchdays' => 0,
            'status' => 'planned',
            'rules' => null,
            'prizes' => null,
            'active' => true,
        ]);

        $this->command->info('Campeonatos y jornadas de prueba creados exitosamente.');
    }
}
