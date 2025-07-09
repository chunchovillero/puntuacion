<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Club;
use Illuminate\Support\Facades\Log;

class CsvDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Rutas posibles del CSV
        $possiblePaths = [
            'C:\Users\SISMA\Downloads\2.csv',
            'C:\Users\SISMA\Desktop\Listado_Inscritos.csv',
            base_path('2.csv'),
            base_path('Listado_Inscritos.csv'),
        ];

        $csvPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $csvPath = $path;
                break;
            }
        }

        if (!$csvPath) {
            $this->command->error('No se encontró el archivo CSV en ninguna de las rutas especificadas.');
            $this->command->info('Rutas buscadas:');
            foreach ($possiblePaths as $path) {
                $this->command->info("- $path");
            }
            return;
        }

        $this->command->info("Procesando archivo CSV: $csvPath");

        // Leer el archivo CSV
        $categories = [];
        $clubs = [];
        $rowCount = 0;

        // Intentar detectar la codificación y leer el archivo
        if (($handle = fopen($csvPath, 'r')) !== false) {
            // Leer la primera línea (encabezados)
            $headers = fgetcsv($handle, 1000, ';');
            
            if (!$headers) {
                $this->command->error('No se pudieron leer los encabezados del CSV.');
                return;
            }

            // Convertir encabezados a UTF-8 si es necesario
            $headers = array_map(function($header) {
                if (!mb_check_encoding($header, 'UTF-8')) {
                    return mb_convert_encoding($header, 'UTF-8', 'ISO-8859-1');
                }
                return $header;
            }, $headers);

            $this->command->info('Encabezados encontrados: ' . implode(', ', $headers));

            // Buscar las columnas relevantes (flexible con los nombres)
            $categoryColumn = $this->findColumn($headers, ['categoria', 'category', 'cat']);
            $clubColumn = $this->findColumn($headers, ['club', 'equipo', 'team']);

            if ($categoryColumn === false) {
                $this->command->error('No se encontró la columna de categoría en el CSV.');
                return;
            }

            if ($clubColumn === false) {
                $this->command->error('No se encontró la columna de club en el CSV.');
                return;
            }

            $this->command->info("Columna de categoría: {$headers[$categoryColumn]}");
            $this->command->info("Columna de club: {$headers[$clubColumn]}");

            // Leer todas las filas
            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                $rowCount++;

                // Convertir toda la fila a UTF-8 si es necesario
                $row = array_map(function($cell) {
                    if (!mb_check_encoding($cell, 'UTF-8')) {
                        return mb_convert_encoding($cell, 'UTF-8', 'ISO-8859-1');
                    }
                    return $cell;
                }, $row);

                // Extraer categoría
                if (isset($row[$categoryColumn]) && !empty(trim($row[$categoryColumn]))) {
                    $category = trim($row[$categoryColumn]);
                    $categories[$category] = true;
                }

                // Extraer club
                if (isset($row[$clubColumn]) && !empty(trim($row[$clubColumn]))) {
                    $club = trim($row[$clubColumn]);
                    $clubs[$club] = true;
                }
            }

            fclose($handle);
        } else {
            $this->command->error('No se pudo abrir el archivo CSV.');
            return;
        }

        $this->command->info("Se procesaron $rowCount filas del CSV.");
        $this->command->info("Categorías únicas encontradas: " . count($categories));
        $this->command->info("Clubes únicos encontrados: " . count($clubs));

        // Insertar categorías
        $categoryCount = 0;
        foreach (array_keys($categories) as $categoryName) {
            if (!Category::where('name', $categoryName)->exists()) {
                Category::create([
                    'name' => $categoryName,
                    'description' => "Categoría importada desde CSV: $categoryName"
                ]);
                $categoryCount++;
                $this->command->info("✓ Categoría creada: $categoryName");
            } else {
                $this->command->warn("○ Categoría ya existe: $categoryName");
            }
        }

        // Insertar clubes
        $clubCount = 0;
        foreach (array_keys($clubs) as $clubName) {
            if (!Club::where('name', $clubName)->exists()) {
                Club::create([
                    'name' => $clubName,
                    'city' => $this->extractCity($clubName),
                    'founded_year' => null
                ]);
                $clubCount++;
                $this->command->info("✓ Club creado: $clubName");
            } else {
                $this->command->warn("○ Club ya existe: $clubName");
            }
        }

        $this->command->info("Resumen:");
        $this->command->info("- Categorías nuevas creadas: $categoryCount");
        $this->command->info("- Clubes nuevos creados: $clubCount");
        $this->command->info("¡Seeder completado exitosamente!");
    }

    /**
     * Buscar una columna en los encabezados (búsqueda flexible)
     */
    private function findColumn($headers, $searchTerms)
    {
        foreach ($headers as $index => $header) {
            $header = strtolower(trim($header));
            foreach ($searchTerms as $term) {
                if (strpos($header, strtolower($term)) !== false) {
                    return $index;
                }
            }
        }
        return false;
    }

    /**
     * Intentar extraer la ciudad del nombre del club
     */
    private function extractCity($clubName)
    {
        // Lista de ciudades comunes de Chile (puedes expandir esta lista)
        $cities = [
            'Santiago', 'Valparaíso', 'Viña del Mar', 'Concepción', 'Antofagasta',
            'Temuco', 'Iquique', 'Rancagua', 'Talca', 'Arica', 'Chillán',
            'Calama', 'La Serena', 'Coquimbo', 'Puerto Montt', 'Punta Arenas',
            'Osorno', 'Valdivia', 'Quillota', 'San Antonio', 'Melipilla',
            'Los Ángeles', 'Curicó', 'Linares', 'Ovalle', 'Copiapó'
        ];

        foreach ($cities as $city) {
            if (stripos($clubName, $city) !== false) {
                return $city;
            }
        }

        // Si no se encuentra una ciudad conocida, retornar null
        return null;
    }
}
