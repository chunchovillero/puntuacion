<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Championship;
use App\Models\Pilot;
use App\Models\ChampionshipRegistration;
use App\Models\MatchdayParticipant;
use App\Models\Category;

class TestMatchdayParticipantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener el primer campeonato y su primera jornada
        $championship = Championship::first();
        if (!$championship) {
            $this->command->error('No hay campeonatos. Ejecuta ChampionshipSeeder primero.');
            return;
        }

        $matchday = $championship->matchdays()->first();
        if (!$matchday) {
            $this->command->error('No hay jornadas en el campeonato.');
            return;
        }

        // Obtener algunos pilotos
        $pilots = Pilot::take(10)->get();
        if ($pilots->count() < 3) {
            $this->command->error('Necesitas al menos 3 pilotos. Ejecuta TestPilotSeeder primero.');
            return;
        }

        // Obtener categorías
        $categories = Category::all();
        if ($categories->count() < 1) {
            $this->command->error('No hay categorías. Ejecuta CategorySeeder primero.');
            return;
        }

        $dorsalCounter = 1;

        foreach ($pilots as $pilot) {
            // Crear registro en campeonato para el piloto
            $championshipRegistration = ChampionshipRegistration::firstOrCreate([
                'championship_id' => $championship->id,
                'pilot_id' => $pilot->id,
            ], [
                'category_id' => $categories->random()->id,
                'bib_number' => $dorsalCounter++,
                'registration_date' => now()->subDays(rand(1, 30)),
                'status' => 'active',
            ]);

            // Crear participación en jornada
            MatchdayParticipant::firstOrCreate([
                'matchday_id' => $matchday->id,
                'pilot_id' => $pilot->id,
            ], [
                'category_id' => $championshipRegistration->category_id,
                'entry_fee_paid' => $matchday->entry_fee ?? 5000,
                'status' => 'confirmed',
                'registered_at' => now()->subDays(rand(1, 7)),
            ]);

            $this->command->info('Participante creado: ' . $pilot->name . ' - Dorsal: ' . $championshipRegistration->bib_number);
        }

        $this->command->info('Participantes de jornada de prueba creados exitosamente!');
    }
}
