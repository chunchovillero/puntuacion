<?php

// Script para verificar la nomenclatura de rondas después de la actualización

require 'vendor/autoload.php';

// Usar las mismas configuraciones que Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RaceSeries;
use App\Models\Matchday;

echo "=== VERIFICACIÓN DE NOMENCLATURA DE RONDAS ===\n\n";

// Verificar las fechas disponibles
$matchdays = Matchday::with('championship')->get();

if ($matchdays->isEmpty()) {
    echo "No hay fechas de carrera disponibles.\n";
    exit;
}

echo "Fechas disponibles:\n";
foreach ($matchdays as $matchday) {
    echo "- ID: {$matchday->id}, Fecha: {$matchday->date}, Campeonato: {$matchday->championship->name}\n";
}

echo "\n=== SERIES/RONDAS POR FECHA ===\n";

foreach ($matchdays as $matchday) {
    echo "\n--- Fecha: {$matchday->date} (ID: {$matchday->id}) ---\n";
    
    $series = RaceSeries::where('matchday_id', $matchday->id)
        ->with('category')
        ->orderBy('category_id')
        ->orderBy('series_number')
        ->get();
    
    if ($series->isEmpty()) {
        echo "  No hay series/rondas para esta fecha.\n";
        continue;
    }
    
    $currentCategory = null;
    foreach ($series as $serie) {
        if ($currentCategory !== $serie->category->name) {
            $currentCategory = $serie->category->name;
            echo "  Categoría: {$currentCategory}\n";
        }
        
        $nomenclatura = strpos($serie->name, 'Serie') !== false ? "❌ ANTIGUA" : "✅ NUEVA";
        echo "    - {$serie->name} (Número: {$serie->series_number}) {$nomenclatura}\n";
    }
}

echo "\n=== RESUMEN ===\n";
$totalSeries = RaceSeries::count();
$seriesConNuevaRonda = RaceSeries::where('name', 'LIKE', 'Ronda %')->count();
$seriesConSerie = RaceSeries::where('name', 'LIKE', 'Serie %')->count();

echo "Total de series/rondas: {$totalSeries}\n";
echo "Con nomenclatura nueva (Ronda N): {$seriesConNuevaRonda}\n";
echo "Con nomenclatura antigua (Serie X): {$seriesConSerie}\n";

if ($seriesConSerie > 0) {
    echo "\n❌ Aún hay {$seriesConSerie} serie(s) con nomenclatura antigua.\n";
    echo "Se recomienda regenerar las series o actualizar manualmente.\n";
} else {
    echo "\n✅ Todas las series usan la nueva nomenclatura 'Ronda N'.\n";
}

echo "\n=== FIN DE VERIFICACIÓN ===\n";
