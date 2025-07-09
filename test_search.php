<?php

use Illuminate\Http\Request;

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Probando búsqueda de pilotos...\n";

// Test 1: Contar todos los pilotos
$totalPilots = App\Models\Pilot::count();
echo "Total pilotos: $totalPilots\n";

// Test 2: Buscar pilotos con 'a' en el nombre
$pilotsWithA = App\Models\Pilot::where('first_name', 'like', '%a%')->count();
echo "Pilotos con 'a' en nombre: $pilotsWithA\n";

// Test 3: Mostrar primeros 3 nombres
echo "Primeros 3 nombres:\n";
$firstNames = App\Models\Pilot::take(3)->pluck('first_name');
foreach ($firstNames as $name) {
    echo "- $name\n";
}

// Test 4: Buscar con el primer nombre
if ($firstNames->isNotEmpty()) {
    $firstName = $firstNames->first();
    $searchResults = App\Models\Pilot::where('first_name', 'like', "%$firstName%")->count();
    echo "Búsqueda por '$firstName': $searchResults resultados\n";
}
