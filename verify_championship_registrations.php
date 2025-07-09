<?php

require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Importar modelos
use App\Models\ChampionshipRegistration;
use App\Models\Championship;

echo "=== VERIFICACIÓN DE REGISTROS EN EL CAMPEONATO ===\n";
echo "Total de registros: " . ChampionshipRegistration::count() . "\n\n";

echo "Registros por estado:\n";
$statusCounts = ChampionshipRegistration::select('status', \DB::raw('count(*) as total'))
    ->groupBy('status')
    ->get();

foreach ($statusCounts as $item) {
    echo "- {$item->status}: {$item->total} pilotos\n";
}

echo "\nRegistros por campeonato:\n";
$championshipCounts = ChampionshipRegistration::select('championship_id', \DB::raw('count(*) as total'))
    ->groupBy('championship_id')
    ->with('championship')
    ->get();

foreach ($championshipCounts as $item) {
    $championship = $item->championship;
    echo "- {$championship->name} ({$championship->year}): {$item->total} pilotos\n";
}

echo "\nAlgunos registros de ejemplo:\n";
$samples = ChampionshipRegistration::with(['pilot', 'category', 'championship'])
    ->limit(5)
    ->get();

foreach ($samples as $registration) {
    echo "- {$registration->pilot->first_name} {$registration->pilot->last_name} ";
    echo "(Dorsal: {$registration->bib_number}) ";
    echo "- Categoría: {$registration->category->name} ";
    echo "- Estado: {$registration->status}\n";
}
