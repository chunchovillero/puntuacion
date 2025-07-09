<?php

// Script para probar el nuevo método getTransferDescription()

require 'vendor/autoload.php';

// Usar las mismas configuraciones que Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RaceSeries;

echo "=== PRUEBA DEL MÉTODO getTransferDescription() ===\n\n";

// Obtener algunas series para probar
$series = RaceSeries::with('category')
    ->where('matchday_id', 34)
    ->orderBy('category_id')
    ->orderBy('series_number')
    ->take(10)
    ->get();

if ($series->isEmpty()) {
    echo "No hay series para probar.\n";
    exit;
}

echo "Probando con las primeras 10 series:\n\n";

foreach ($series as $serie) {
    echo "--- {$serie->category->name} - {$serie->name} ---\n";
    echo "Transfer to final: {$serie->transfer_to_final}\n";
    echo "Transfer to semifinal: {$serie->transfer_to_semifinal}\n";
    echo "Transfer to quarterfinal: {$serie->transfer_to_quarterfinal}\n";
    echo "Método anterior: {$serie->getTotalTransfers()} avanzan\n";
    echo "Método nuevo: {$serie->getTransferDescription()}\n";
    echo "----------------------------------------\n\n";
}

echo "=== CASOS DE PRUEBA ESPECÍFICOS ===\n\n";

// Crear algunos casos de prueba específicos para probar diferentes combinaciones
$testCases = [
    ['final' => 4, 'semifinal' => 0, 'quarterfinal' => 0, 'descripcion' => 'Solo final'],
    ['final' => 2, 'semifinal' => 2, 'quarterfinal' => 0, 'descripcion' => 'Final y semifinal'],
    ['final' => 1, 'semifinal' => 1, 'quarterfinal' => 1, 'descripcion' => 'Todas las etapas'],
    ['final' => 0, 'semifinal' => 3, 'quarterfinal' => 0, 'descripcion' => 'Solo semifinal'],
    ['final' => 0, 'semifinal' => 0, 'quarterfinal' => 0, 'descripcion' => 'Sin transferencias'],
];

foreach ($testCases as $test) {
    // Crear un objeto temporal para probar
    $tempSerie = new RaceSeries();
    $tempSerie->transfer_to_final = $test['final'];
    $tempSerie->transfer_to_semifinal = $test['semifinal'];
    $tempSerie->transfer_to_quarterfinal = $test['quarterfinal'];
    
    echo "Caso: {$test['descripcion']}\n";
    echo "Final: {$test['final']}, Semifinal: {$test['semifinal']}, Cuartos: {$test['quarterfinal']}\n";
    echo "Resultado: {$tempSerie->getTransferDescription()}\n";
    echo "---\n\n";
}

echo "=== FIN DE PRUEBAS ===\n";
