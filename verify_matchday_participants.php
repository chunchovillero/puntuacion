<?php

require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Importar modelos
use App\Models\MatchdayParticipant;
use App\Models\Matchday;
use App\Models\ChampionshipRegistration;

echo "=== VERIFICACIÓN DE REGISTROS EN JORNADAS ===\n";
echo "Total de participantes en jornadas: " . MatchdayParticipant::count() . "\n\n";

echo "Participantes por jornada:\n";
$matchdays = Matchday::withCount('participants')->get();

foreach ($matchdays as $matchday) {
    echo "- {$matchday->name}: {$matchday->participants_count} participantes\n";
}

echo "\nParticipantes por estado:\n";
$statusCounts = MatchdayParticipant::select('status', \DB::raw('count(*) as total'))
    ->groupBy('status')
    ->get();

foreach ($statusCounts as $item) {
    echo "- {$item->status}: {$item->total} participantes\n";
}

echo "\nEjemplos de participantes:\n";
$samples = MatchdayParticipant::with(['pilot', 'matchday', 'category'])
    ->limit(5)
    ->get();

foreach ($samples as $participant) {
    echo "- {$participant->pilot->first_name} {$participant->pilot->last_name} ";
    echo "en {$participant->matchday->name} ";
    echo "(#{$participant->registration_number}) ";
    echo "- Categoría: {$participant->category->name} ";
    echo "- Estado: {$participant->status}";
    echo "- Pagó: $" . number_format($participant->entry_fee_paid, 0) . "\n";
}

echo "\nComparación con registros del campeonato:\n";
$activeChampionshipRegistrations = ChampionshipRegistration::where('status', 'active')->count();
echo "- Pilotos activos en campeonato: {$activeChampionshipRegistrations}\n";
echo "- Total de participaciones en jornadas: " . MatchdayParticipant::count() . "\n";
echo "- Promedio de participaciones por piloto: " . round(MatchdayParticipant::count() / $activeChampionshipRegistrations, 2) . "\n";
