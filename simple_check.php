<?php

// Script simple para verificar jornadas
// Ejecutar con: php -r "include 'simple_check.php';"

// Autoload
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Verificando jornadas...\n";

// Obtener todas las jornadas
$matchdays = \App\Models\Matchday::select('id', 'name', 'championship_id')->orderBy('id')->get();

echo "Total de jornadas: " . $matchdays->count() . "\n";

if ($matchdays->count() > 0) {
    echo "IDs existentes:\n";
    foreach ($matchdays as $md) {
        echo "- ID: {$md->id}, Nombre: {$md->name}, Campeonato: {$md->championship_id}\n";
    }
    
    $minId = $matchdays->min('id');
    $maxId = $matchdays->max('id');
    echo "\nRango de IDs: {$minId} - {$maxId}\n";
    
    // Verificar específicamente ID 27
    $matchday27 = $matchdays->where('id', 27)->first();
    if ($matchday27) {
        echo "\n✓ Jornada 27 EXISTE: {$matchday27->name}\n";
    } else {
        echo "\n✗ Jornada 27 NO EXISTE\n";
    }
} else {
    echo "No hay jornadas en la base de datos\n";
}
