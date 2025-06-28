<?php

// Test script to debug championship relationship issues
require_once 'vendor/autoload.php';

use App\Models\Championship;
use App\Models\Matchday;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing Championship relationships...\n";
    
    $championship = Championship::first();
    if ($championship) {
        echo "Championship found: " . $championship->name . "\n";
        echo "ID: " . $championship->id . "\n";
        
        // Test direct matchdays access
        echo "Testing direct matchdays relationship...\n";
        $matchdays = $championship->matchdays;
        echo "Matchdays count: " . $matchdays->count() . "\n";
        
        foreach ($matchdays as $matchday) {
            echo "- Matchday {$matchday->number}: {$matchday->venue} (Status: {$matchday->status})\n";
        }
        
    } else {
        echo "No championships found!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
