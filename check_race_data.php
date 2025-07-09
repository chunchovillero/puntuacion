<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Verificando datos de planilla de carreras:" . PHP_EOL;

$matchdayId = 31;

// Verificar jornada
$matchday = \App\Models\Matchday::find($matchdayId);
if ($matchday) {
    echo "✓ Jornada encontrada: {$matchday->name}" . PHP_EOL;
    echo "  - Campeonato ID: {$matchday->championship_id}" . PHP_EOL;
} else {
    echo "✗ Jornada no encontrada" . PHP_EOL;
    exit;
}

// Verificar series
$series = \App\Models\RaceSeries::where('matchday_id', $matchdayId)->count();
echo "✓ Series creadas: {$series}" . PHP_EOL;

// Verificar participantes
$participants = \App\Models\MatchdayParticipant::where('matchday_id', $matchdayId)->count();
echo "✓ Participantes inscritos: {$participants}" . PHP_EOL;

// Verificar si hay participantes con registros del campeonato
$participantsWithRegistration = \App\Models\MatchdayParticipant::where('matchday_id', $matchdayId)
    ->with('pilot.championshipRegistrations')
    ->get()
    ->filter(function($participant) use ($matchday) {
        return $participant->pilot->championshipRegistrations
            ->where('championship_id', $matchday->championship_id)
            ->isNotEmpty();
    });

echo "✓ Participantes con registro del campeonato: {$participantsWithRegistration->count()}" . PHP_EOL;

if ($participantsWithRegistration->count() > 0) {
    echo "  Ejemplo de dorsales:" . PHP_EOL;
    foreach ($participantsWithRegistration->take(5) as $participant) {
        $registration = $participant->pilot->championshipRegistrations
            ->where('championship_id', $matchday->championship_id)
            ->first();
        $dorsal = $registration ? $registration->bib_number : 'N/A';
        echo "    - {$participant->pilot->full_name}: Dorsal {$dorsal}" . PHP_EOL;
    }
}
