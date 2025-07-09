<?php

require_once 'bootstrap/app.php';

use App\Models\Category;
use App\Models\Championship;
use App\Models\ChampionshipRegistration;

echo "Verificando datos de categorías y campeonatos:\n";
echo "Total categorías: " . Category::count() . "\n";
echo "Categorías activas: " . Category::where('active', true)->count() . "\n";
echo "Total campeonatos: " . Championship::count() . "\n";
echo "Campeonatos activos: " . Championship::where('active', true)->count() . "\n";
echo "Total registros: " . ChampionshipRegistration::count() . "\n";

echo "\nPrimeras 3 categorías con pilotos:\n";
Category::withCount('pilots')->limit(3)->get()->each(function($cat) {
    echo "- {$cat->name} ({$cat->type}): {$cat->pilots_count} pilotos\n";
});

echo "\nCampeonatos activos:\n";
Championship::where('active', true)->orderBy('year', 'desc')->get()->each(function($champ) {
    echo "- {$champ->name} ({$champ->year})\n";
});

echo "\nEjemplo de conteos por categoría y campeonato:\n";
$category = Category::withCount('pilots')->first();
if ($category) {
    echo "Categoría: {$category->name}\n";
    $championships = Championship::where('active', true)->get();
    foreach ($championships as $championship) {
        $count = ChampionshipRegistration::where('category_id', $category->id)
            ->where('championship_id', $championship->id)
            ->count();
        echo "  - {$championship->name}: {$count} registros\n";
    }
}
