<?php

// Script para probar que la vista de edición de series funciona correctamente

require 'vendor/autoload.php';

// Usar las mismas configuraciones que Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RaceSeries;

echo "=== PRUEBA DE PÁGINAS DE EDICIÓN DE SERIES ===\n\n";

// Obtener algunas series para probar
$series = RaceSeries::with(['category', 'matchday'])
    ->where('matchday_id', 34)
    ->take(3)
    ->get();

if ($series->isEmpty()) {
    echo "No hay series para probar.\n";
    exit;
}

echo "URLs de edición disponibles para prueba:\n\n";

foreach ($series as $serie) {
    $editUrl = "http://localhost:8000/admin/matchdays/{$serie->matchday_id}/race-sheets/series/{$serie->id}/edit";
    echo "- {$serie->category->name} - {$serie->name}\n";
    echo "  URL: {$editUrl}\n\n";
}

echo "=== Datos de prueba ===\n";
$firstSeries = $series->first();
echo "Primera serie para prueba:\n";
echo "ID: {$firstSeries->id}\n";
echo "Nombre: {$firstSeries->name}\n";
echo "Categoría: {$firstSeries->category->name}\n";
echo "Transferencias: {$firstSeries->getTransferDescription()}\n";
echo "URL completa: http://localhost:8000/admin/matchdays/{$firstSeries->matchday_id}/race-sheets/series/{$firstSeries->id}/edit\n";

echo "\n=== FIN DE PRUEBA ===\n";
