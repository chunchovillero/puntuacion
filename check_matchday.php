<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICACIÓN DE JORNADA 27 ===\n";

try {
    // Verificar si existe la jornada 27
    $matchday = App\Models\Matchday::find(27);
    
    if ($matchday) {
        echo "✓ Jornada 27 ENCONTRADA:\n";
        echo "  - ID: {$matchday->id}\n";
        echo "  - Nombre: {$matchday->name}\n";
        echo "  - Número: {$matchday->number}\n";
        echo "  - Lugar: {$matchday->venue}\n";
        echo "  - Fecha: {$matchday->date}\n";
        echo "  - Estado: {$matchday->status}\n";
        echo "  - Campeonato ID: {$matchday->championship_id}\n";
        
        if ($matchday->championship) {
            echo "  - Campeonato: {$matchday->championship->name}\n";
        }
    } else {
        echo "✗ Jornada 27 NO EXISTE en la base de datos\n";
    }
    
    // Mostrar algunas jornadas existentes
    echo "\n=== JORNADAS EXISTENTES ===\n";
    $matchdays = App\Models\Matchday::orderBy('id')->take(10)->get();
    
    if ($matchdays->count() > 0) {
        echo "IDs existentes: ";
        echo $matchdays->pluck('id')->implode(', ');
        echo "\n";
        
        echo "Total de jornadas: " . App\Models\Matchday::count() . "\n";
        
        // Mostrar rango de IDs
        $minId = App\Models\Matchday::min('id');
        $maxId = App\Models\Matchday::max('id');
        echo "Rango de IDs: {$minId} - {$maxId}\n";
    } else {
        echo "No hay jornadas en la base de datos\n";
    }
    
    // Verificar si existe alguna jornada cerca del ID 27
    echo "\n=== JORNADAS CERCANAS AL ID 27 ===\n";
    $nearbyMatchdays = App\Models\Matchday::whereBetween('id', [20, 35])->get();
    
    if ($nearbyMatchdays->count() > 0) {
        foreach ($nearbyMatchdays as $md) {
            echo "ID {$md->id}: {$md->name} (Campeonato: {$md->championship_id})\n";
        }
    } else {
        echo "No hay jornadas con IDs entre 20 y 35\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
