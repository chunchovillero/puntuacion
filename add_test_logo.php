<?php

// Script temporal para agregar un logo de prueba
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Club;

try {
    // Crear un archivo SVG simple como logo de prueba
    $svgContent = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
  <circle cx="50" cy="50" r="40" fill="#3498db" />
  <text x="50" y="55" text-anchor="middle" fill="white" font-family="Arial" font-size="24" font-weight="bold">BMX</text>
</svg>';
    
    // Guardar el archivo SVG
    $logoPath = 'logos/bmx-logo-example.svg';
    $fullPath = storage_path('app/public/' . $logoPath);
    file_put_contents($fullPath, $svgContent);
    
    // Actualizar el primer club con este logo
    $club = Club::first();
    if ($club) {
        $club->logo = $logoPath;
        $club->save();
        echo "Logo agregado al club: " . $club->name . "\n";
        echo "Ruta del logo: " . $logoPath . "\n";
    } else {
        echo "No se encontraron clubes en la base de datos.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
