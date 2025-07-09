<?php

require_once __DIR__ . '/vendor/autoload.php';

// Test championship registrations
use App\Models\Matchday;
use App\Models\MatchdayParticipant;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Championship Registration Relations\n";
echo "==========================================\n\n";

// Get a matchday with participants
$matchday = Matchday::with(['participants.pilot.championshipRegistrations.category'])
                   ->whereHas('participants')
                   ->first();

if (!$matchday) {
    echo "No matchday with participants found.\n";
    exit;
}

echo "Matchday: {$matchday->name}\n";
echo "Championship ID: {$matchday->championship_id}\n";
echo "Participants: {$matchday->participants->count()}\n\n";

foreach ($matchday->participants as $participant) {
    $pilot = $participant->pilot;
    $championshipRegistrations = $pilot->championshipRegistrations
        ->where('championship_id', $matchday->championship_id);
    
    echo "Pilot: {$pilot->full_name}\n";
    echo "  - Championship Registrations: {$championshipRegistrations->count()}\n";
    
    if ($championshipRegistrations->count() > 0) {
        $registration = $championshipRegistrations->first();
        echo "  - Bib Number: " . ($registration->bib_number ?? 'Not assigned') . "\n";
        echo "  - Category ID: " . ($registration->category_id ?? 'Not assigned') . "\n";
        echo "  - Category: " . ($registration->category ? $registration->category->name : 'Category not found') . "\n";
    } else {
        echo "  - No championship registration found!\n";
    }
    echo "\n";
}
