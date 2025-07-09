<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Verificando tablas de planilla de carreras:" . PHP_EOL;

$tables = ['race_series', 'race_heats', 'race_lineups'];

foreach($tables as $table) {
    try {
        $count = DB::table($table)->count();
        echo "✓ $table: existe (registros: $count)" . PHP_EOL;
    } catch(Exception $e) {
        echo "✗ $table: ERROR - " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "Verificando estructura de race_series:" . PHP_EOL;
try {
    $columns = DB::select("DESCRIBE race_series");
    foreach($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})" . PHP_EOL;
    }
} catch(Exception $e) {
    echo "Error al obtener estructura: " . $e->getMessage() . PHP_EOL;
}
