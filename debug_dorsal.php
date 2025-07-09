<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\MatchdayParticipant;
use App\Models\Matchday;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Debug Participant Dorsal Issue\n";
echo "==============================\n\n";

// Get matchday with participants
$matchday = Matchday::with([
    'participants.pilot.championshipRegistrations' => function($query) {
        // Get all championship registrations to debug
    }
])->whereHas('participants')->first();

if (!$matchday) {
    echo "No matchday with participants found.\n";
    exit;
}

echo "Matchday: {$matchday->name}\n";
echo "Championship ID: {$matchday->championship_id}\n\n";

foreach ($matchday->participants as $participant) {
    echo "=== Participant ===\n";
    echo "Pilot: {$participant->pilot->full_name}\n";
    echo "All Championship Registrations: {$participant->pilot->championshipRegistrations->count()}\n";
    
    foreach ($participant->pilot->championshipRegistrations as $reg) {
        echo "  - Championship {$reg->championship_id}: Bib #{$reg->bib_number}\n";
    }
    
    $correctReg = $participant->pilot->championshipRegistrations
        ->where('championship_id', $matchday->championship_id)
        ->first();
        
    echo "Correct registration for this championship: ";
    if ($correctReg) {
        echo "Bib #{$correctReg->bib_number}\n";
    } else {
        echo "NOT FOUND\n";
    }
    echo "\n";
}
