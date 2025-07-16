<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUG CONTROLADOR EXACTO ===\n";

try {
    // Encontrar la jornada exactamente como lo hace Laravel con Model Binding
    $matchday = App\Models\Matchday::findOrFail(27);
    
    echo "✓ Jornada encontrada: {$matchday->name}\n";
    echo "✓ Estado: {$matchday->status}\n";
    
    // Simular el código exacto del controlador
    $isPublicView = false; // Simular usuario autenticado
    
    if ($isPublicView && $matchday->status === 'cancelled') {
        echo "✗ Jornada cancelada para vista pública\n";
        exit(1);
    }
    
    echo "\n=== Cargando relaciones (código exacto del controlador) ===\n";
    
    // Cargar relaciones exactamente como en el controlador
    try {
        $matchday->load([
            'championship',
            'organizerClub',
            'participants' => function($query) {
                $query->with(['pilot.club', 'pilot.category'])
                      ->whereIn('status', ['registered', 'confirmed', 'active'])
                      ->orderBy('created_at', 'asc');
            }
        ]);
        
        echo "✓ Relaciones cargadas exitosamente\n";
        echo "✓ Championship: " . ($matchday->championship ? $matchday->championship->name : 'NULL') . "\n";
        echo "✓ Organizer Club: " . ($matchday->organizerClub ? $matchday->organizerClub->name : 'NULL') . "\n";
        echo "✓ Participants: " . $matchday->participants->count() . "\n";
        
        // Verificar algunos participantes
        $sampleParticipants = $matchday->participants->take(3);
        foreach ($sampleParticipants as $i => $participant) {
            echo "   Participant " . ($i+1) . ": {$participant->pilot->name}\n";
            echo "     Club: " . ($participant->pilot->club ? $participant->pilot->club->name : 'NULL') . "\n";
            echo "     Category: " . ($participant->pilot->category ? $participant->pilot->category->name : 'NULL') . "\n";
        }
        
    } catch (Exception $e) {
        echo "✗ Error al cargar relaciones: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        exit(1);
    }
    
    // Cargar conteos
    try {
        $matchday->loadCount('participants');
        echo "✓ Load count exitoso: {$matchday->participants_count}\n";
    } catch (Exception $e) {
        echo "✗ Error en loadCount: " . $e->getMessage() . "\n";
        exit(1);
    }
    
    // Simular la respuesta JSON
    try {
        $response = [
            'success' => true,
            'data' => $matchday->toArray()
        ];
        echo "✓ Respuesta JSON generada exitosamente\n";
        echo "✓ Tamaño de respuesta: " . strlen(json_encode($response)) . " bytes\n";
    } catch (Exception $e) {
        echo "✗ Error al generar JSON: " . $e->getMessage() . "\n";
        exit(1);
    }
    
    echo "\n=== ¡CÓDIGO DEL CONTROLADOR EJECUTADO SIN ERRORES! ===\n";
    
} catch (Exception $e) {
    echo "ERROR FATAL: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
