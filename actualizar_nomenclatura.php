<?php

// Script para actualizar la nomenclatura de series existentes de "Serie X" a "Ronda N"

require 'vendor/autoload.php';

// Usar las mismas configuraciones que Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RaceSeries;
use Illuminate\Support\Facades\DB;

echo "=== ACTUALIZACIÓN DE NOMENCLATURA DE SERIES ===\n\n";

// Obtener todas las series que tienen nomenclatura antigua
$seriesToUpdate = RaceSeries::where('name', 'LIKE', 'Serie %')->get();

if ($seriesToUpdate->isEmpty()) {
    echo "✅ No hay series con nomenclatura antigua para actualizar.\n";
    exit;
}

echo "Encontradas {$seriesToUpdate->count()} series con nomenclatura antigua.\n\n";

$updated = 0;
$errors = 0;

DB::transaction(function () use ($seriesToUpdate, &$updated, &$errors) {
    foreach ($seriesToUpdate as $series) {
        try {
            // Convertir "Serie A" -> "Ronda 1", "Serie B" -> "Ronda 2", etc.
            $oldName = $series->name;
            $newName = "Ronda {$series->series_number}";
            
            $series->update(['name' => $newName]);
            
            echo "✅ Actualizado: '{$oldName}' -> '{$newName}' (ID: {$series->id})\n";
            $updated++;
            
        } catch (Exception $e) {
            echo "❌ Error actualizando serie ID {$series->id}: {$e->getMessage()}\n";
            $errors++;
        }
    }
});

echo "\n=== RESUMEN DE ACTUALIZACIÓN ===\n";
echo "Series actualizadas exitosamente: {$updated}\n";
echo "Errores encontrados: {$errors}\n";

if ($errors === 0) {
    echo "\n✅ Todas las series fueron actualizadas correctamente.\n";
} else {
    echo "\n⚠️ Hubo algunos errores durante la actualización.\n";
}

echo "\n=== VERIFICACIÓN FINAL ===\n";
$totalSeries = RaceSeries::count();
$seriesConNuevaRonda = RaceSeries::where('name', 'LIKE', 'Ronda %')->count();
$seriesConSerie = RaceSeries::where('name', 'LIKE', 'Serie %')->count();

echo "Total de series: {$totalSeries}\n";
echo "Con nomenclatura nueva (Ronda N): {$seriesConNuevaRonda}\n";
echo "Con nomenclatura antigua (Serie X): {$seriesConSerie}\n";

if ($seriesConSerie === 0) {
    echo "\n🎉 ¡Éxito! Todas las series ahora usan la nomenclatura 'Ronda N'.\n";
} else {
    echo "\n⚠️ Aún quedan {$seriesConSerie} series con nomenclatura antigua.\n";
}

echo "\n=== FIN DE ACTUALIZACIÓN ===\n";
