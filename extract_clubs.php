<?php

// Script temporal para extraer clubes únicos del CSV

$csvFile = 'C:\Users\SISMA\Downloads\2.csv';

if (!file_exists($csvFile)) {
    die("Archivo CSV no encontrado: $csvFile\n");
}

$clubs = [];

// Leer el archivo CSV
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Leer la primera línea (encabezados)
    $headers = fgetcsv($handle, 1000, ";");
    
    // Encontrar el índice de la columna CLUB
    $clubIndex = array_search('CLUB', $headers);
    
    if ($clubIndex === false) {
        die("Columna 'CLUB' no encontrada en el CSV\n");
    }
    
    // Leer cada línea del archivo
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        if (isset($data[$clubIndex]) && !empty(trim($data[$clubIndex]))) {
            $clubName = trim($data[$clubIndex]);
            $clubs[$clubName] = true; // Usar como clave para evitar duplicados
        }
    }
    
    fclose($handle);
}

// Convertir a array y ordenar
$uniqueClubs = array_keys($clubs);
sort($uniqueClubs);

echo "=== CLUBES ÚNICOS EXTRAÍDOS DEL CSV ===\n";
echo "Total de clubes únicos: " . count($uniqueClubs) . "\n\n";

foreach ($uniqueClubs as $index => $club) {
    echo ($index + 1) . ". " . $club . "\n";
}

echo "\n=== FORMATO PARA SEEDER ===\n";
foreach ($uniqueClubs as $club) {
    $slug = strtolower(str_replace([' ', 'Ñ', 'ñ'], ['-', 'n', 'n'], $club));
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    echo "['name' => '$club', 'slug' => '$slug', 'active' => true],\n";
}

?>
