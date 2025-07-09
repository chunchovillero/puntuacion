<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Jornadas disponibles:" . PHP_EOL;

$matchdays = \App\Models\Matchday::with('championship')->get();

if($matchdays->count() > 0) {
    foreach($matchdays as $matchday) {
        echo "ID: {$matchday->id} - {$matchday->championship->name} - {$matchday->name} ({$matchday->date})" . PHP_EOL;
    }
} else {
    echo "No hay jornadas disponibles" . PHP_EOL;
}

echo PHP_EOL . "Categorías disponibles:" . PHP_EOL;
$categories = \App\Models\Category::all();

if($categories->count() > 0) {
    foreach($categories as $category) {
        echo "ID: {$category->id} - {$category->name}" . PHP_EOL;
    }
} else {
    echo "No hay categorías disponibles" . PHP_EOL;
}
