<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Verificando nomenclatura de rondas:" . PHP_EOL;

$matchdayId = 34;

// Verificar jornada
$matchday = \App\Models\Matchday::find($matchdayId);
if (!$matchday) {
    echo "✗ Jornada no encontrada" . PHP_EOL;
    exit;
}

echo "✓ Jornada: {$matchday->name}" . PHP_EOL;

// Verificar rondas existentes
$existingSeries = \App\Models\RaceSeries::where('matchday_id', $matchdayId)->get();
echo "✓ Rondas existentes: {$existingSeries->count()}" . PHP_EOL;

if ($existingSeries->count() > 0) {
    echo "  Nombres actuales:" . PHP_EOL;
    foreach ($existingSeries as $series) {
        echo "    - {$series->name} (ID: {$series->id})" . PHP_EOL;
    }
} else {
    echo "  No hay rondas creadas aún" . PHP_EOL;
}

// Verificar participantes por categoría
$participantsByCategory = \App\Models\MatchdayParticipant::where('matchday_id', $matchdayId)
    ->with('category')
    ->where('status', '!=', 'cancelled')
    ->get()
    ->groupBy('category.name');

echo PHP_EOL . "Participantes por categoría:" . PHP_EOL;
foreach ($participantsByCategory as $categoryName => $participants) {
    $count = $participants->count();
    $rondasNecesarias = ceil($count / 8); // Asumiendo 8 pilotos por ronda
    echo "  - {$categoryName}: {$count} participantes → {$rondasNecesarias} ronda(s)" . PHP_EOL;
}
