<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pilot;
use App\Models\Club;
use App\Models\Category;
use Carbon\Carbon;

class PilotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Verificar que existen clubes y categorías
        $clubs = Club::where('status', 'active')->get();
        $categories = Category::where('active', true)->get();
        
        if ($clubs->isEmpty() || $categories->isEmpty()) {
            $this->command->error('No se encontraron clubes activos o categorías. Ejecuta ClubSeeder y CategorySeeder primero.');
            return;
        }

        // Limpiar tabla antes de insertar (opcional - comentado por seguridad)
        // Pilot::truncate();

        // Datos base para generar pilotos
        $firstNames = [
            'varones' => ['Carlos', 'Diego', 'Felipe', 'Sebastian', 'Mateo', 'Nicolas', 'Benjamin', 'Vicente', 'Joaquin', 'Maximiliano', 'Tomas', 'Cristobal', 'Martin', 'Lucas', 'Andres', 'Jose', 'Francisco', 'Gabriel', 'Eduardo', 'Pablo'],
            'mujeres' => ['Sofia', 'Valentina', 'Isabella', 'Camila', 'Valeria', 'Renata', 'Amanda', 'Esperanza', 'Agustina', 'Emilia', 'Antonia', 'Martina', 'Francisca', 'Josefa', 'Catalina', 'Trinidad', 'Constanza', 'Javiera', 'Maite', 'Carolina']
        ];
        
        $lastNames = ['Gonzalez', 'Munoz', 'Rojas', 'Diaz', 'Perez', 'Soto', 'Contreras', 'Silva', 'Martinez', 'Sepulveda', 'Morales', 'Rodriguez', 'Lopez', 'Fuentes', 'Hernandez', 'Torres', 'Araya', 'Flores', 'Espinoza', 'Valdes', 'Castillo', 'Reyes', 'Garrido', 'Vargas', 'Tapia', 'Santander', 'Riquelme', 'Vera', 'Herrera', 'Medina'];
        
        $bikebrands = ['GT', 'Haro', 'Redline', 'Mongoose', 'Fit', 'Cult', 'Sunday', 'WeThePeople', 'Premium', 'Kink', 'Eastern', 'Hoffman', 'Chase', 'DK', 'Mirraco'];
        
        $specialties = [
            ['Velocidad', 'Técnica'],
            ['Saltos', 'Curvas'],
            ['Arranque', 'Sprint final'],
            ['Estrategia', 'Consistencia'],
            ['Agilidad', 'Resistencia']
        ];

        $pilotsData = [];

        // Generar pilotos para cada club
        foreach ($clubs as $club) {
            $pilotsPerClub = rand(7, 30); // Entre 7 y 30 pilotos por club
            
            for ($i = 0; $i < $pilotsPerClub; $i++) {
                // Seleccionar categoría aleatoria
                $category = $categories->random();
                
                // Determinar género basado en la categoría
                $gender = 'male';
                if ($category->gender === 'mujeres') {
                    $gender = 'female';
                } elseif ($category->gender === null) {
                    $gender = rand(0, 1) ? 'male' : 'female';
                }
                
                // Determinar edad basada en la categoría
                $age = $this->getRandomAgeForCategory($category);
                $birthDate = Carbon::now()->subYears($age)->subDays(rand(0, 365));
                
                // Seleccionar nombres según género
                $firstNameArray = $gender === 'female' ? $firstNames['mujeres'] : $firstNames['varones'];
                $firstName = $firstNameArray[array_rand($firstNameArray)];
                $lastName = $lastNames[array_rand($lastNames)] . ' ' . $lastNames[array_rand($lastNames)];
                
                // Generar RUT único
                $rut = $this->generateUniqueRut();
                
                $pilotData = [
                    'club_id' => $club->id,
                    'category_id' => $category->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'rut' => $rut,
                    'nickname' => $this->generateNickname($firstName),
                    'description' => "Piloto de BMX del club {$club->name}",
                    'birth_date' => $birthDate,
                    'age' => $age,
                    'gender' => $gender,
                    'phone' => '+569' . rand(10000000, 99999999),
                    'email' => 'piloto' . rand(1000, 9999) . '@email.com',
                    'emergency_contact_name' => 'Contacto ' . rand(1, 999),
                    'emergency_contact_phone' => '+569' . rand(10000000, 99999999),
                    'photo' => null, // Se puede agregar después
                    'bike_brand' => $bikebrands[array_rand($bikebrands)],
                    'bike_model' => 'Race Pro',
                    'bike_year' => rand(2018, 2025),
                    'specialties' => $specialties[array_rand($specialties)],
                    'achievements' => $this->generateAchievements(),
                    'joined_club_date' => Carbon::now()->subDays(rand(30, 1000)),
                    'status' => 'active',
                    'weight' => $this->getWeightForAge($age),
                    'height' => $this->getHeightForAge($age),
                    'blood_type' => ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'][array_rand(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'])],
                    'medical_conditions' => rand(0, 10) > 8 ? 'Ninguna conocida' : null,
                    'insurance_provider' => ['Fonasa', 'Isapre Banmédica', 'Isapre Consalud', 'Isapre Cruz Blanca'][array_rand(['Fonasa', 'Isapre Banmédica', 'Isapre Consalud', 'Isapre Cruz Blanca'])],
                    'insurance_number' => rand(100000, 999999),
                    'social_media' => [
                        'instagram' => '@piloto' . rand(100, 999)
                    ],
                    'ranking_points' => rand(0, 1000)
                ];
                
                $pilotsData[] = $pilotData;
            }
        }

        // Crear pilotos
        $created = 0;
        foreach ($pilotsData as $pilotData) {
            // Verificar si ya existe un piloto con el mismo RUT
            $existing = Pilot::where('rut', $pilotData['rut'])->first();
            
            if (!$existing) {
                Pilot::create($pilotData);
                $created++;
                $this->command->info("Piloto creado: {$pilotData['first_name']} {$pilotData['last_name']} - Club: " . Club::find($pilotData['club_id'])->name);
            } else {
                $this->command->warn("Piloto ya existe con RUT: {$pilotData['rut']}");
            }
        }

        $this->command->info("PilotSeeder ejecutado exitosamente.");
        $this->command->info("Total pilotos creados: {$created}");
        $this->command->info("Total pilotos por club:");
        
        foreach ($clubs as $club) {
            $count = Pilot::where('club_id', $club->id)->count();
            $this->command->info("- {$club->name}: {$count} pilotos");
        }
    }

    /**
     * Obtener edad aleatoria para una categoría
     */
    private function getRandomAgeForCategory($category)
    {
        $minAge = $category->age_min ?? 5;
        $maxAge = $category->age_max ?? 45;
        
        // Ajustar rangos extremos
        if ($minAge < 5) $minAge = 5;
        if ($maxAge > 45) $maxAge = 45;
        if ($maxAge < $minAge) $maxAge = $minAge + 5;
        
        return rand($minAge, $maxAge);
    }

    /**
     * Generar RUT único
     */
    private function generateUniqueRut()
    {
        do {
            $numero = rand(10000000, 25000000);
            $dv = $this->calculateDV($numero);
            $rut = $numero . '-' . $dv;
        } while (Pilot::where('rut', $rut)->exists());
        
        return $rut;
    }

    /**
     * Calcular dígito verificador del RUT
     */
    private function calculateDV($numero)
    {
        $suma = 0;
        $multiplicador = 2;
        
        while ($numero > 0) {
            $suma += ($numero % 10) * $multiplicador;
            $numero = intval($numero / 10);
            $multiplicador = $multiplicador == 7 ? 2 : $multiplicador + 1;
        }
        
        $resto = $suma % 11;
        $dv = 11 - $resto;
        
        if ($dv == 11) return '0';
        if ($dv == 10) return 'K';
        
        return (string)$dv;
    }

    /**
     * Generar nickname
     */
    private function generateNickname($firstName)
    {
        $nicknames = ['Speedy', 'Racer', 'Turbo', 'Flash', 'Rocket', 'Storm', 'Thunder', 'Lightning', 'Blaze', 'Maverick'];
        return rand(0, 1) ? $nicknames[array_rand($nicknames)] : substr($firstName, 0, 3) . rand(10, 99);
    }

    /**
     * Generar logros
     */
    private function generateAchievements()
    {
        $achievements = [
            'Campeón Regional 2024',
            'Mejor Tiempo Clasificatorio',
            'Top 5 Nacional',
            'Medalla de Bronce Interclub',
            'Mejor Novato 2023',
            'Podio en 3 carreras consecutivas'
        ];
        
        $numAchievements = rand(0, 3);
        if ($numAchievements == 0) return null;
        
        $selected = array_rand($achievements, min($numAchievements, count($achievements)));
        if (!is_array($selected)) $selected = [$selected];
        
        return array_map(function($index) use ($achievements) {
            return $achievements[$index];
        }, $selected);
    }

    /**
     * Obtener peso apropiado para la edad
     */
    private function getWeightForAge($age)
    {
        if ($age <= 8) return rand(20, 30);
        if ($age <= 12) return rand(25, 45);
        if ($age <= 16) return rand(35, 65);
        if ($age <= 25) return rand(50, 85);
        if ($age <= 35) return rand(55, 90);
        return rand(60, 95);
    }

    /**
     * Obtener altura apropiada para la edad
     */
    private function getHeightForAge($age)
    {
        if ($age <= 8) return rand(110, 130);
        if ($age <= 12) return rand(125, 155);
        if ($age <= 16) return rand(140, 175);
        if ($age <= 25) return rand(155, 185);
        if ($age <= 35) return rand(160, 185);
        return rand(160, 180);
    }
}
