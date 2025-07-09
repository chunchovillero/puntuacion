<?php

// Script temporal para extraer jornadas existentes en la base de datos

require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Matchday;
use App\Models\Championship;

echo "=== JORNADAS (MATCHDAYS) EXISTENTES EN LA BASE DE DATOS ===\n\n";

try {
    // Obtener todas las jornadas con información del campeonato
    $matchdays = Matchday::with('championship')->orderBy('date', 'asc')->get();
    
    if ($matchdays->count() > 0) {
        echo "Total de jornadas encontradas: " . $matchdays->count() . "\n\n";
        
        foreach ($matchdays as $index => $matchday) {
            echo ($index + 1) . ". " . $matchday->name . "\n";
            echo "   ID: " . $matchday->id . "\n";
            echo "   Campeonato: " . ($matchday->championship ? $matchday->championship->name : 'Sin campeonato') . "\n";
            echo "   Fecha: " . $matchday->date->format('Y-m-d') . "\n";
            echo "   Hora: " . ($matchday->start_time ? $matchday->start_time : 'No definida') . "\n";
            echo "   Lugar: " . ($matchday->location ? $matchday->location : 'No definido') . "\n";
            echo "   Estado: " . $matchday->status . "\n";
            echo "   Descripción: " . ($matchday->description ? substr($matchday->description, 0, 100) . '...' : 'Sin descripción') . "\n";
            echo "   Creado: " . $matchday->created_at->format('Y-m-d H:i:s') . "\n";
            echo "   ----------------------------------------\n";
        }
        
        echo "\n=== FORMATO PARA SEEDER ===\n";
        echo "[\n";
        foreach ($matchdays as $matchday) {
            $championshipId = $matchday->championship_id ?: 'null';
            echo "    [\n";
            echo "        'name' => '" . addslashes($matchday->name) . "',\n";
            echo "        'championship_id' => $championshipId,\n";
            echo "        'date' => '" . $matchday->date->format('Y-m-d') . "',\n";
            echo "        'start_time' => " . ($matchday->start_time ? "'" . $matchday->start_time . "'" : 'null') . ",\n";
            echo "        'end_time' => " . ($matchday->end_time ? "'" . $matchday->end_time . "'" : 'null') . ",\n";
            echo "        'location' => " . ($matchday->location ? "'" . addslashes($matchday->location) . "'" : 'null') . ",\n";
            echo "        'description' => " . ($matchday->description ? "'" . addslashes($matchday->description) . "'" : 'null') . ",\n";
            echo "        'max_participants' => " . ($matchday->max_participants ?: 'null') . ",\n";
            echo "        'registration_deadline' => " . ($matchday->registration_deadline ? "'" . $matchday->registration_deadline->format('Y-m-d H:i:s') . "'" : 'null') . ",\n";
            echo "        'registration_fee' => " . ($matchday->registration_fee ?: '0') . ",\n";
            echo "        'status' => '" . $matchday->status . "',\n";
            echo "        'weather_conditions' => " . ($matchday->weather_conditions ? "'" . addslashes($matchday->weather_conditions) . "'" : 'null') . ",\n";
            echo "        'track_conditions' => " . ($matchday->track_conditions ? "'" . addslashes($matchday->track_conditions) . "'" : 'null') . ",\n";
            echo "        'created_at' => '" . $matchday->created_at->format('Y-m-d H:i:s') . "',\n";
            echo "        'updated_at' => '" . $matchday->updated_at->format('Y-m-d H:i:s') . "',\n";
            echo "    ],\n";
        }
        echo "];\n";
        
    } else {
        echo "No se encontraron jornadas en la base de datos.\n";
    }
    
} catch (Exception $e) {
    echo "Error al consultar la base de datos: " . $e->getMessage() . "\n";
}

echo "\n=== CAMPEONATOS DISPONIBLES ===\n";
try {
    $championships = Championship::orderBy('name')->get();
    if ($championships->count() > 0) {
        foreach ($championships as $championship) {
            echo "ID: " . $championship->id . " - " . $championship->name . " (" . $championship->status . ")\n";
        }
    } else {
        echo "No se encontraron campeonatos en la base de datos.\n";
    }
} catch (Exception $e) {
    echo "Error al consultar campeonatos: " . $e->getMessage() . "\n";
}

?>
