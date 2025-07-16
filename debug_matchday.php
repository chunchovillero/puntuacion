<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUG API MATCHDAY 27 ===\n";

try {
    // Encontrar la jornada 27
    $matchday = App\Models\Matchday::find(27);
    
    if (!$matchday) {
        echo "ERROR: Jornada 27 no encontrada\n";
        exit(1);
    }
    
    echo "✓ Jornada encontrada: {$matchday->name}\n";
    
    // Simular el código del controlador apiShow
    echo "\n=== Probando relaciones ===\n";
    
    // 1. Championship
    echo "1. Cargando championship...\n";
    try {
        $championship = $matchday->championship;
        echo "   ✓ Championship: " . ($championship ? $championship->name : 'NULL') . "\n";
    } catch (Exception $e) {
        echo "   ✗ Error en championship: " . $e->getMessage() . "\n";
    }
    
    // 2. Organizer Club
    echo "2. Cargando organizerClub...\n";
    try {
        $organizerClub = $matchday->organizerClub;
        echo "   ✓ Organizer Club: " . ($organizerClub ? $organizerClub->name : 'NULL') . "\n";
    } catch (Exception $e) {
        echo "   ✗ Error en organizerClub: " . $e->getMessage() . "\n";
    }
    
    // 3. Participants
    echo "3. Cargando participants...\n";
    try {
        $participants = $matchday->participants()->whereIn('status', ['registered', 'confirmed', 'active'])->get();
        echo "   ✓ Participants: " . $participants->count() . " encontrados\n";
        
        // Probar carga de relaciones anidadas
        if ($participants->count() > 0) {
            echo "4. Cargando relaciones de participants...\n";
            foreach ($participants->take(3) as $i => $participant) {
                echo "   Participant " . ($i+1) . ":\n";
                
                try {
                    $pilot = $participant->pilot;
                    echo "     ✓ Pilot: " . ($pilot ? $pilot->name : 'NULL') . "\n";
                    
                    if ($pilot) {
                        $club = $pilot->club;
                        echo "     ✓ Club: " . ($club ? $club->name : 'NULL') . "\n";
                        
                        $category = $pilot->category;
                        echo "     ✓ Category: " . ($category ? $category->name : 'NULL') . "\n";
                    }
                } catch (Exception $e) {
                    echo "     ✗ Error en participant " . ($i+1) . ": " . $e->getMessage() . "\n";
                }
            }
        }
    } catch (Exception $e) {
        echo "   ✗ Error en participants: " . $e->getMessage() . "\n";
    }
    
    // 5. Load count
    echo "5. Cargando loadCount...\n";
    try {
        $matchday->loadCount('participants');
        echo "   ✓ Participants count: " . $matchday->participants_count . "\n";
    } catch (Exception $e) {
        echo "   ✗ Error en loadCount: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Todo completado sin errores ===\n";
    
} catch (Exception $e) {
    echo "ERROR GENERAL: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
