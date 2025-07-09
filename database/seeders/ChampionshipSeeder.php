<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Championship;
use App\Models\Matchday;
use App\Models\Club;
use App\Models\Pilot;
use App\Models\Category;
use App\Models\ChampionshipRegistration;
use App\Models\MatchdayParticipant;
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
        // Obtener el año actual
        $currentYear = Carbon::now()->year;
        
        // Verificar si ya existe un campeonato para el año actual
        $existingChampionship = Championship::where('year', $currentYear)->first();
        
        if ($existingChampionship) {
            $this->command->warn("Ya existe un campeonato para el año {$currentYear}: {$existingChampionship->name}");
            $this->command->info("Verificando y creando jornadas faltantes...");
            $championship = $existingChampionship;
        } else {
            // Crear campeonato para el año actual
            $championship = Championship::create([
                'name' => "Campeonato Nacional BMX {$currentYear}",
                'year' => $currentYear,
                'description' => "Campeonato oficial de BMX para el año {$currentYear}. Incluye todas las categorías y modalidades oficiales.",
                'start_date' => Carbon::createFromDate($currentYear, 3, 1),
                'end_date' => Carbon::createFromDate($currentYear, 11, 30),
                'total_matchdays' => 8,
                'status' => 'active',
                'rules' => [
                    'Reglas oficiales UCI BMX Racing',
                    'Categorías por edad y género',
                    'Sistema de puntuación acumulativo',
                    'Mínimo 5 jornadas para clasificar al ranking final'
                ],
                'entry_fee' => 5000.00,
                'prizes' => 'Trofeos, medallas y premios en efectivo para los primeros 3 lugares de cada categoría',
                'active' => true,
            ]);

            $this->command->info("Campeonato '{$championship->name}' creado exitosamente.");
        }

        // Obtener algunos clubes existentes para organizar jornadas
        $clubs = Club::where('status', 'active')->limit(5)->get();
        
        if ($clubs->isEmpty()) {
            $this->command->error('No se encontraron clubes activos. Ejecuta ClubSeeder primero.');
            return;
        }

        // Definir las jornadas que queremos crear
        $matchdaysData = [
            [
                'name' => "Jornada 1 - Marzo {$currentYear}",
                'date' => Carbon::createFromDate($currentYear, 3, 15),
                'start_time' => '09:00:00',
                'status' => Carbon::now() > Carbon::createFromDate($currentYear, 3, 15) ? 'completed' : 'scheduled',
                'description' => "Primera jornada del Campeonato Nacional BMX {$currentYear}",
                'weather_conditions' => Carbon::now() > Carbon::createFromDate($currentYear, 3, 15) ? 'Soleado' : null,
                'track_conditions' => Carbon::now() > Carbon::createFromDate($currentYear, 3, 15) ? 'Excelentes' : null,
            ],
            [
                'name' => "Jornada 2 - Abril {$currentYear}",
                'date' => Carbon::createFromDate($currentYear, 4, 20),
                'start_time' => '09:15:00',
                'status' => Carbon::now() > Carbon::createFromDate($currentYear, 4, 20) ? 'completed' : 'scheduled',
                'description' => "Segunda jornada del Campeonato Nacional BMX {$currentYear}",
                'weather_conditions' => Carbon::now() > Carbon::createFromDate($currentYear, 4, 20) ? 'Nublado' : null,
                'track_conditions' => Carbon::now() > Carbon::createFromDate($currentYear, 4, 20) ? 'Buenas' : null,
            ],
            [
                'name' => "Jornada 3 - Mayo {$currentYear}",
                'date' => Carbon::createFromDate($currentYear, 5, 18),
                'start_time' => '09:00:00',
                'status' => Carbon::now() > Carbon::createFromDate($currentYear, 5, 18) ? 'completed' : 'scheduled',
                'description' => "Tercera jornada del Campeonato Nacional BMX {$currentYear}",
                'weather_conditions' => Carbon::now() > Carbon::createFromDate($currentYear, 5, 18) ? 'Parcialmente nublado' : null,
                'track_conditions' => Carbon::now() > Carbon::createFromDate($currentYear, 5, 18) ? 'Buenas' : null,
            ],
            [
                'name' => "Jornada 4 - Junio {$currentYear}",
                'date' => Carbon::createFromDate($currentYear, 6, 22),
                'start_time' => '09:00:00',
                'status' => Carbon::now() > Carbon::createFromDate($currentYear, 6, 22) ? 'completed' : 'scheduled',
                'description' => "Cuarta jornada del Campeonato Nacional BMX {$currentYear}",
                'weather_conditions' => Carbon::now() > Carbon::createFromDate($currentYear, 6, 22) ? 'Soleado' : null,
                'track_conditions' => Carbon::now() > Carbon::createFromDate($currentYear, 6, 22) ? 'Excelentes' : null,
            ],
            [
                'name' => "Jornada 5 - Julio {$currentYear}",
                'date' => Carbon::createFromDate($currentYear, 7, 20),
                'start_time' => '09:00:00',
                'status' => Carbon::now() > Carbon::createFromDate($currentYear, 7, 20) ? 'completed' : 'scheduled',
                'description' => "Quinta jornada del Campeonato Nacional BMX {$currentYear}",
                'weather_conditions' => null,
                'track_conditions' => null,
            ],
            [
                'name' => "Jornada 6 - Agosto {$currentYear}",
                'date' => Carbon::createFromDate($currentYear, 8, 17),
                'start_time' => '09:00:00',
                'status' => 'scheduled',
                'description' => "Sexta jornada del Campeonato Nacional BMX {$currentYear}",
                'weather_conditions' => null,
                'track_conditions' => null,
            ],
            [
                'name' => "Jornada 7 - Septiembre {$currentYear}",
                'date' => Carbon::createFromDate($currentYear, 9, 21),
                'start_time' => '09:00:00',
                'status' => 'scheduled',
                'description' => "Séptima jornada del Campeonato Nacional BMX {$currentYear}",
                'weather_conditions' => null,
                'track_conditions' => null,
            ],
            [
                'name' => "Jornada 8 - Octubre {$currentYear}",
                'date' => Carbon::createFromDate($currentYear, 10, 19),
                'start_time' => '09:00:00',
                'status' => 'scheduled',
                'description' => "Octava y última jornada del Campeonato Nacional BMX {$currentYear}",
                'weather_conditions' => null,
                'track_conditions' => null,
            ],
        ];

        // Crear las jornadas
        $createdMatchdays = 0;
        $existingMatchdays = 0;

        foreach ($matchdaysData as $index => $matchdayData) {
            $journeyNumber = $index + 1;
            
            // Verificar si ya existe una jornada con el mismo championship_id y number
            $existing = Matchday::where('championship_id', $championship->id)
                              ->where('number', $journeyNumber)
                              ->first();
            
            if (!$existing) {
                // Asignar club organizador rotativo
                $organizerClub = $clubs->get($index % $clubs->count());
                
                Matchday::create([
                    'championship_id' => $championship->id,
                    'number' => $journeyNumber,
                    'name' => $matchdayData['name'],
                    'date' => $matchdayData['date'],
                    'start_time' => $matchdayData['start_time'],
                    'end_time' => null,
                    'venue' => "Pista {$organizerClub->name}",
                    'address' => "Dirección de la pista {$organizerClub->name}",
                    'organizer_club_id' => $organizerClub->id,
                    'description' => $matchdayData['description'],
                    'entry_fee' => 5000.00,
                    'status' => $matchdayData['status'],
                ]);
                
                $createdMatchdays++;
                $this->command->info("Jornada creada: {$matchdayData['name']} - {$matchdayData['date']->format('Y-m-d')}");
            } else {
                $existingMatchdays++;
                $this->command->warn("Jornada ya existe: {$matchdayData['name']} - {$matchdayData['date']->format('Y-m-d')}");
            }
        }

        // Mostrar resumen
        $this->command->info('=== RESUMEN DEL SEEDER ===');
        $this->command->info("Campeonato: {$championship->name}");
        $this->command->info("Año: {$currentYear}");
        $this->command->info("Jornadas creadas: {$createdMatchdays}");
        $this->command->info("Jornadas existentes: {$existingMatchdays}");
        $this->command->info("Total jornadas: " . ($createdMatchdays + $existingMatchdays));
        $this->command->info("Cuota de inscripción: $" . number_format($championship->entry_fee, 0, ',', '.'));
        
        // Contar jornadas por estado
        $completedCount = Matchday::where('championship_id', $championship->id)->where('status', 'completed')->count();
        $scheduledCount = Matchday::where('championship_id', $championship->id)->where('status', 'scheduled')->count();
        
        $this->command->info("Jornadas completadas: {$completedCount}");
        $this->command->info("Jornadas programadas: {$scheduledCount}");
        
        // Registrar todos los pilotos en el campeonato con categorías aleatorias
        $this->registerPilotsInChampionship($championship);
        
        // Registrar los pilotos registrados en el campeonato en cada jornada
        $this->registerPilotsInMatchdays($championship);
    }
    
    /**
     * Registra todos los pilotos en el campeonato con categorías aleatorias
     */
    private function registerPilotsInChampionship(Championship $championship)
    {
        $this->command->info('=== REGISTRANDO PILOTOS EN EL CAMPEONATO ===');
        
        // Obtener todos los pilotos activos
        $pilots = Pilot::where('status', 'active')->get();
        
        if ($pilots->isEmpty()) {
            $this->command->warn('No se encontraron pilotos activos. Ejecuta PilotSeeder primero.');
            return;
        }
        
        // Obtener todas las categorías activas
        $categories = Category::where('active', true)->get();
        
        if ($categories->isEmpty()) {
            $this->command->error('No se encontraron categorías activas. Ejecuta CategorySeeder primero.');
            return;
        }
        
        $totalRegistrations = 0;
        $totalSkipped = 0;
        
        $this->command->info("Procesando registro en {$championship->name}...");
        
        foreach ($pilots as $pilot) {
            // Verificar si el piloto ya está registrado en este campeonato
            $existingRegistration = ChampionshipRegistration::where('championship_id', $championship->id)
                                                           ->where('pilot_id', $pilot->id)
                                                           ->first();
            
            if ($existingRegistration) {
                $totalSkipped++;
                continue;
            }
            
            // Filtrar categorías que son apropiadas para la edad y género del piloto
            $appropriateCategories = $categories->filter(function ($category) use ($pilot) {
                // Verificar género (si la categoría especifica género)
                if ($category->gender && $category->gender !== 'mixed' && $category->gender !== $pilot->gender) {
                    return false;
                }
                
                // Verificar edad (si la categoría especifica rango de edad)
                if ($pilot->age) {
                    if ($category->age_min && $pilot->age < $category->age_min) {
                        return false;
                    }
                    if ($category->age_max && $pilot->age > $category->age_max) {
                        return false;
                    }
                }
                
                return true;
            });
            
            // Si no hay categorías apropiadas, usar una categoría aleatoria
            $selectedCategory = $appropriateCategories->isNotEmpty() 
                ? $appropriateCategories->random() 
                : $categories->random();
            
            // Generar número de dorsal único para este campeonato
            $bibNumber = $this->generateBibNumber($championship->id);
            
            // Determinar estado aleatorio (más probable que esté activo)
            $statuses = ['active', 'active', 'active', 'inactive']; // 75% activo
            $status = $statuses[array_rand($statuses)];
            
            // Crear el registro en el campeonato
            ChampionshipRegistration::create([
                'championship_id' => $championship->id,
                'pilot_id' => $pilot->id,
                'category_id' => $selectedCategory->id,
                'bib_number' => $bibNumber,
                'status' => $status,
                'registration_date' => $this->generateRandomRegistrationDate($championship->start_date),
                'notes' => $this->generateRandomNotes(),
            ]);
            
            $totalRegistrations++;
        }
        
        $this->command->info('=== RESUMEN DE REGISTROS EN EL CAMPEONATO ===');
        $this->command->info("Total de pilotos: {$pilots->count()}");
        $this->command->info("Registros creados: {$totalRegistrations}");
        $this->command->info("Registros omitidos (ya existían): {$totalSkipped}");
        $this->command->info("Categorías disponibles: {$categories->count()}");
    }
    
    /**
     * Genera un número de dorsal único para el campeonato
     */
    private function generateBibNumber($championshipId)
    {
        do {
            $number = rand(1, 999);
        } while (ChampionshipRegistration::where('championship_id', $championshipId)->where('bib_number', $number)->exists());
        
        return $number;
    }
    
    /**
     * Genera notas aleatorias para el registro
     */
    private function generateRandomNotes()
    {
        $notes = [
            null,
            null,
            null, // 60% sin notas
            'Piloto experimentado',
            'Primera participación en campeonato',
            'Piloto prometedor',
            'Veterano del BMX',
            'Categoría especial por edad',
            'Inscripción tardía',
            'Recomendado por el club',
            'Piloto local destacado',
            'Participante regular',
        ];
        
        return $notes[array_rand($notes)];
    }
    
    /**
     * Genera una fecha de registro aleatoria antes de la fecha de inicio del campeonato
     */
    private function generateRandomRegistrationDate($championshipStartDate)
    {
        $startCarbon = Carbon::parse($championshipStartDate);
        $daysBeforeMin = 1;
        $daysBeforeMax = 60; // Hasta 2 meses antes
        
        $daysBefore = rand($daysBeforeMin, $daysBeforeMax);
        $registrationDate = $startCarbon->copy()->subDays($daysBefore);
        
        return $registrationDate;
    }
    
    /**
     * Registra los pilotos del campeonato en cada jornada
     */
    private function registerPilotsInMatchdays(Championship $championship)
    {
        $this->command->info('=== REGISTRANDO PILOTOS EN JORNADAS ===');
        
        // Obtener todas las jornadas del campeonato
        $matchdays = Matchday::where('championship_id', $championship->id)->get();
        
        if ($matchdays->isEmpty()) {
            $this->command->warn('No se encontraron jornadas para este campeonato.');
            return;
        }
        
        // Obtener todos los pilotos registrados activos en el campeonato
        $championshipRegistrations = ChampionshipRegistration::where('championship_id', $championship->id)
                                                            ->where('status', 'active')
                                                            ->with(['pilot', 'category'])
                                                            ->get();
        
        if ($championshipRegistrations->isEmpty()) {
            $this->command->warn('No se encontraron pilotos registrados activos en el campeonato.');
            return;
        }
        
        $totalRegistrations = 0;
        $totalSkipped = 0;
        
        foreach ($matchdays as $matchday) {
            $this->command->info("Procesando {$matchday->name}...");
            
            $registrationsThisMatchday = 0;
            $skippedThisMatchday = 0;
            
            // Decidir qué porcentaje de pilotos participará en esta jornada (80-95%)
            $participationRate = rand(80, 95) / 100;
            $participantsCount = (int) ($championshipRegistrations->count() * $participationRate);
            
            // Seleccionar pilotos aleatorios para esta jornada
            $selectedRegistrations = $championshipRegistrations->random($participantsCount);
            
            foreach ($selectedRegistrations as $registration) {
                // Verificar si el piloto ya está registrado en esta jornada
                $existingParticipant = MatchdayParticipant::where('matchday_id', $matchday->id)
                                                        ->where('pilot_id', $registration->pilot_id)
                                                        ->first();
                
                if ($existingParticipant) {
                    $skippedThisMatchday++;
                    continue;
                }
                
                // Generar número de registro único para esta jornada
                $registrationNumber = $this->generateMatchdayRegistrationNumber($matchday->id);
                
                // Determinar estado del participante (más probable que esté confirmado)
                $statuses = ['registered', 'confirmed', 'confirmed', 'confirmed']; // 75% confirmado
                $status = $statuses[array_rand($statuses)];
                
                // Determinar si pagó la cuota (85% probabilidad de haber pagado)
                $entryFeePaid = rand(1, 100) <= 85 ? $matchday->entry_fee : 0;
                
                // Crear la participación en la jornada
                MatchdayParticipant::create([
                    'matchday_id' => $matchday->id,
                    'pilot_id' => $registration->pilot_id,
                    'category_id' => $registration->category_id, // Usar la misma categoría del campeonato
                    'registration_number' => $registrationNumber,
                    'entry_fee_paid' => $entryFeePaid,
                    'status' => $status,
                    'notes' => $this->generateMatchdayNotes(),
                    'registered_at' => $this->generateMatchdayRegistrationDate($matchday->date),
                ]);
                
                $registrationsThisMatchday++;
            }
            
            $this->command->info("  - Registros creados: {$registrationsThisMatchday}");
            $this->command->info("  - Registros omitidos (ya existían): {$skippedThisMatchday}");
            $this->command->info("  - Tasa de participación: " . round(($registrationsThisMatchday / $championshipRegistrations->count()) * 100, 1) . "%");
            
            $totalRegistrations += $registrationsThisMatchday;
            $totalSkipped += $skippedThisMatchday;
        }
        
        $this->command->info('=== RESUMEN DE REGISTROS EN JORNADAS ===');
        $this->command->info("Total de jornadas: {$matchdays->count()}");
        $this->command->info("Pilotos disponibles: {$championshipRegistrations->count()}");
        $this->command->info("Total de registros en jornadas creados: {$totalRegistrations}");
        $this->command->info("Total de registros omitidos: {$totalSkipped}");
        $this->command->info("Promedio de participantes por jornada: " . round($totalRegistrations / $matchdays->count(), 1));
    }
    
    /**
     * Genera un número de registro único para la jornada
     */
    private function generateMatchdayRegistrationNumber($matchdayId)
    {
        do {
            $number = 'REG' . str_pad($matchdayId, 2, '0', STR_PAD_LEFT) . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (MatchdayParticipant::where('matchday_id', $matchdayId)->where('registration_number', $number)->exists());
        
        return $number;
    }
    
    /**
     * Genera notas aleatorias para el participante de la jornada
     */
    private function generateMatchdayNotes()
    {
        $notes = [
            null,
            null,
            null,
            null, // 70% sin notas
            'Inscripción confirmada',
            'Primera vez en esta pista',
            'Piloto regular',
            'Inscripción de último momento',
            'Recomendado por el organizador',
            'Piloto visitante',
            'Categoría especial',
            'Necesita supervisión',
            'Piloto experimentado en esta pista',
        ];
        
        return $notes[array_rand($notes)];
    }
    
    /**
     * Genera una fecha de registro aleatoria antes de la fecha de la jornada
     */
    private function generateMatchdayRegistrationDate($matchdayDate)
    {
        $matchdayCarbon = Carbon::parse($matchdayDate);
        $daysBeforeMin = 1;
        $daysBeforeMax = 21; // Hasta 3 semanas antes
        
        $daysBefore = rand($daysBeforeMin, $daysBeforeMax);
        $registrationDate = $matchdayCarbon->copy()->subDays($daysBefore);
        
        // Agregar algo de variación en la hora
        $registrationDate->setTime(rand(8, 20), rand(0, 59), rand(0, 59));
        
        return $registrationDate;
    }
}
