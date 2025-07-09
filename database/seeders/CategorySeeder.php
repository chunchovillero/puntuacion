<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Limpiar tabla antes de insertar
        Category::truncate();

        $categories = [
            // ARO 26
            ['name' => 'ARO 26 VARONES 16 y -', 'type' => 'aro', 'gender' => 'varones', 'age_min' => null, 'age_max' => 16, 'description' => 'ARO 26 VARONES 16 y -', 'active' => true],
            ['name' => 'ARO 26 VARONES 17 y +', 'type' => 'aro', 'gender' => 'varones', 'age_min' => 17, 'age_max' => null, 'description' => 'ARO 26 VARONES 17 y +', 'active' => true],
            
            // BALANCE
            ['name' => 'BALANCE 3 y -', 'type' => 'balance', 'gender' => null, 'age_min' => null, 'age_max' => 3, 'description' => 'BALANCE 3 y -', 'active' => true],
            ['name' => 'BALANCE 4', 'type' => 'balance', 'gender' => null, 'age_min' => 4, 'age_max' => 4, 'description' => 'BALANCE 4', 'active' => true],
            ['name' => 'BALANCE 5', 'type' => 'balance', 'gender' => null, 'age_min' => 5, 'age_max' => 5, 'description' => 'BALANCE 5', 'active' => true],
            
            // CRUCEROS DAMAS
            ['name' => 'CRUCEROS DAMAS 12 y +', 'type' => 'cruceros', 'gender' => 'mujeres', 'age_min' => 12, 'age_max' => null, 'description' => 'CRUCEROS DAMAS 12 y +', 'active' => true],
            ['name' => 'CRUCEROS DAMAS 13 a 16', 'type' => 'cruceros', 'gender' => 'mujeres', 'age_min' => 13, 'age_max' => 16, 'description' => 'CRUCEROS DAMAS 13 a 16', 'active' => true],
            ['name' => 'CRUCEROS DAMAS 17 a 29', 'type' => 'cruceros', 'gender' => 'mujeres', 'age_min' => 17, 'age_max' => 29, 'description' => 'CRUCEROS DAMAS 17 a 29', 'active' => true],
            ['name' => 'CRUCEROS DAMAS 30 a 39', 'type' => 'cruceros', 'gender' => 'mujeres', 'age_min' => 30, 'age_max' => 39, 'description' => 'CRUCEROS DAMAS 30 a 39', 'active' => true],
            ['name' => 'CRUCEROS DAMAS 40 y +', 'type' => 'cruceros', 'gender' => 'mujeres', 'age_min' => 40, 'age_max' => null, 'description' => 'CRUCEROS DAMAS 40 y +', 'active' => true],
            
            // CRUCEROS VARONES
            ['name' => 'CRUCEROS VARONES 12 y -', 'type' => 'cruceros', 'gender' => 'varones', 'age_min' => null, 'age_max' => 12, 'description' => 'CRUCEROS VARONES 12 y -', 'active' => true],
            ['name' => 'CRUCEROS VARONES 13 a 14', 'type' => 'cruceros', 'gender' => 'varones', 'age_min' => 13, 'age_max' => 14, 'description' => 'CRUCEROS VARONES 13 a 14', 'active' => true],
            ['name' => 'CRUCEROS VARONES 15 a 16', 'type' => 'cruceros', 'gender' => 'varones', 'age_min' => 15, 'age_max' => 16, 'description' => 'CRUCEROS VARONES 15 a 16', 'active' => true],
            ['name' => 'CRUCEROS VARONES 17 a 24', 'type' => 'cruceros', 'gender' => 'varones', 'age_min' => 17, 'age_max' => 24, 'description' => 'CRUCEROS VARONES 17 a 24', 'active' => true],
            ['name' => 'CRUCEROS VARONES 25 a 29', 'type' => 'cruceros', 'gender' => 'varones', 'age_min' => 25, 'age_max' => 29, 'description' => 'CRUCEROS VARONES 25 a 29', 'active' => true],
            ['name' => 'CRUCEROS VARONES 30 a 34', 'type' => 'cruceros', 'gender' => 'varones', 'age_min' => 30, 'age_max' => 34, 'description' => 'CRUCEROS VARONES 30 a 34', 'active' => true],
            ['name' => 'CRUCEROS VARONES 35 a 39', 'type' => 'cruceros', 'gender' => 'varones', 'age_min' => 35, 'age_max' => 39, 'description' => 'CRUCEROS VARONES 35 a 39', 'active' => true],
            ['name' => 'CRUCEROS VARONES 40 a 44', 'type' => 'cruceros', 'gender' => 'varones', 'age_min' => 40, 'age_max' => 44, 'description' => 'CRUCEROS VARONES 40 a 44', 'active' => true],
            ['name' => 'CRUCEROS VARONES 45 a 49', 'type' => 'cruceros', 'gender' => 'varones', 'age_min' => 45, 'age_max' => 49, 'description' => 'CRUCEROS VARONES 45 a 49', 'active' => true],
            ['name' => 'CRUCEROS VARONES 50 y +', 'type' => 'cruceros', 'gender' => 'varones', 'age_min' => 50, 'age_max' => null, 'description' => 'CRUCEROS VARONES 50 y +', 'active' => true],
            
            // DAMAS
            ['name' => 'DAMAS 6 y -', 'type' => 'damas', 'gender' => 'mujeres', 'age_min' => null, 'age_max' => 6, 'description' => 'DAMAS 6 y -', 'active' => true],
            ['name' => 'DAMAS 7', 'type' => 'damas', 'gender' => 'mujeres', 'age_min' => 7, 'age_max' => 7, 'description' => 'DAMAS 7', 'active' => true],
            ['name' => 'DAMAS 8', 'type' => 'damas', 'gender' => 'mujeres', 'age_min' => 8, 'age_max' => 8, 'description' => 'DAMAS 8', 'active' => true],
            ['name' => 'DAMAS 9', 'type' => 'damas', 'gender' => 'mujeres', 'age_min' => 9, 'age_max' => 9, 'description' => 'DAMAS 9', 'active' => true],
            ['name' => 'DAMAS 10', 'type' => 'damas', 'gender' => 'mujeres', 'age_min' => 10, 'age_max' => 10, 'description' => 'DAMAS 10', 'active' => true],
            ['name' => 'DAMAS 11 y 12', 'type' => 'damas', 'gender' => 'mujeres', 'age_min' => 11, 'age_max' => 12, 'description' => 'DAMAS 11 y 12', 'active' => true],
            ['name' => 'DAMAS 13 y 14', 'type' => 'damas', 'gender' => 'mujeres', 'age_min' => 13, 'age_max' => 14, 'description' => 'DAMAS 13 y 14', 'active' => true],
            ['name' => 'DAMAS 15 y 16', 'type' => 'damas', 'gender' => 'mujeres', 'age_min' => 15, 'age_max' => 16, 'description' => 'DAMAS 15 y 16', 'active' => true],
            ['name' => 'DAMAS 17 a 24', 'type' => 'damas', 'gender' => 'mujeres', 'age_min' => 17, 'age_max' => 24, 'description' => 'DAMAS 17 a 24', 'active' => true],
            ['name' => 'DAMAS 25 y +', 'type' => 'damas', 'gender' => 'mujeres', 'age_min' => 25, 'age_max' => null, 'description' => 'DAMAS 25 y +', 'active' => true],
            
            // DINOSAURIOS
            ['name' => 'DINOSAURIOS', 'type' => 'dinosaurios', 'gender' => 'varones', 'age_min' => 35, 'age_max' => null, 'description' => 'DINOSAURIOS', 'active' => true],
            ['name' => 'DINOSAURIOS 13', 'type' => 'dinosaurios', 'gender' => 'varones', 'age_min' => 35, 'age_max' => null, 'description' => 'DINOSAURIOS 13', 'active' => true],
            
            // ELITE
            ['name' => 'ELITE DAMAS', 'type' => 'elite', 'gender' => 'mujeres', 'age_min' => 17, 'age_max' => null, 'description' => 'ELITE DAMAS', 'active' => true],
            ['name' => 'ELITE DAMAS 1', 'type' => 'elite', 'gender' => 'mujeres', 'age_min' => 17, 'age_max' => null, 'description' => 'ELITE DAMAS 1', 'active' => true],
            ['name' => 'ELITE VARONES', 'type' => 'elite', 'gender' => 'varones', 'age_min' => 17, 'age_max' => null, 'description' => 'ELITE VARONES', 'active' => true],
            ['name' => 'ELITE VARONES 2', 'type' => 'elite', 'gender' => 'varones', 'age_min' => 17, 'age_max' => null, 'description' => 'ELITE VARONES 2', 'active' => true],
            
            // ESCUELAS DAMAS
            ['name' => 'ESCUELAS DAMAS 6 y -', 'type' => 'escuelas', 'gender' => 'mujeres', 'age_min' => null, 'age_max' => 6, 'description' => 'ESCUELAS DAMAS 6 y -', 'active' => true],
            ['name' => 'ESCUELAS DAMAS 7', 'type' => 'escuelas', 'gender' => 'mujeres', 'age_min' => 7, 'age_max' => 7, 'description' => 'ESCUELAS DAMAS 7', 'active' => true],
            ['name' => 'ESCUELAS DAMAS 8', 'type' => 'escuelas', 'gender' => 'mujeres', 'age_min' => 8, 'age_max' => 8, 'description' => 'ESCUELAS DAMAS 8', 'active' => true],
            ['name' => 'ESCUELAS DAMAS 9', 'type' => 'escuelas', 'gender' => 'mujeres', 'age_min' => 9, 'age_max' => 9, 'description' => 'ESCUELAS DAMAS 9', 'active' => true],
            ['name' => 'ESCUELAS DAMAS 10', 'type' => 'escuelas', 'gender' => 'mujeres', 'age_min' => 10, 'age_max' => 10, 'description' => 'ESCUELAS DAMAS 10', 'active' => true],
            ['name' => 'ESCUELAS DAMAS 11', 'type' => 'escuelas', 'gender' => 'mujeres', 'age_min' => 11, 'age_max' => 11, 'description' => 'ESCUELAS DAMAS 11', 'active' => true],
            ['name' => 'ESCUELAS DAMAS 12', 'type' => 'escuelas', 'gender' => 'mujeres', 'age_min' => 12, 'age_max' => 12, 'description' => 'ESCUELAS DAMAS 12', 'active' => true],
            ['name' => 'ESCUELAS DAMAS 13', 'type' => 'escuelas', 'gender' => 'mujeres', 'age_min' => 13, 'age_max' => 13, 'description' => 'ESCUELAS DAMAS 13', 'active' => true],
            ['name' => 'ESCUELAS DAMAS 14', 'type' => 'escuelas', 'gender' => 'mujeres', 'age_min' => 14, 'age_max' => 14, 'description' => 'ESCUELAS DAMAS 14', 'active' => true],
            ['name' => 'ESCUELAS DAMAS 14 a 17', 'type' => 'escuelas', 'gender' => 'mujeres', 'age_min' => 14, 'age_max' => 17, 'description' => 'ESCUELAS DAMAS 14 a 17', 'active' => true],
            
            // ESCUELAS VARONES
            ['name' => 'ESCUELAS VARONES 6 y -', 'type' => 'escuelas', 'gender' => 'varones', 'age_min' => null, 'age_max' => 6, 'description' => 'ESCUELAS VARONES 6 y -', 'active' => true],
            ['name' => 'ESCUELAS VARONES 7', 'type' => 'escuelas', 'gender' => 'varones', 'age_min' => 7, 'age_max' => 7, 'description' => 'ESCUELAS VARONES 7', 'active' => true],
            ['name' => 'ESCUELAS VARONES 8', 'type' => 'escuelas', 'gender' => 'varones', 'age_min' => 8, 'age_max' => 8, 'description' => 'ESCUELAS VARONES 8', 'active' => true],
            ['name' => 'ESCUELAS VARONES 9', 'type' => 'escuelas', 'gender' => 'varones', 'age_min' => 9, 'age_max' => 9, 'description' => 'ESCUELAS VARONES 9', 'active' => true],
            ['name' => 'ESCUELAS VARONES 10', 'type' => 'escuelas', 'gender' => 'varones', 'age_min' => 10, 'age_max' => 10, 'description' => 'ESCUELAS VARONES 10', 'active' => true],
            ['name' => 'ESCUELAS VARONES 11', 'type' => 'escuelas', 'gender' => 'varones', 'age_min' => 11, 'age_max' => 11, 'description' => 'ESCUELAS VARONES 11', 'active' => true],
            ['name' => 'ESCUELAS VARONES 12', 'type' => 'escuelas', 'gender' => 'varones', 'age_min' => 12, 'age_max' => 12, 'description' => 'ESCUELAS VARONES 12', 'active' => true],
            ['name' => 'ESCUELAS VARONES 13', 'type' => 'escuelas', 'gender' => 'varones', 'age_min' => 13, 'age_max' => 13, 'description' => 'ESCUELAS VARONES 13', 'active' => true],
            ['name' => 'ESCUELAS VARONES 14', 'type' => 'escuelas', 'gender' => 'varones', 'age_min' => 14, 'age_max' => 14, 'description' => 'ESCUELAS VARONES 14', 'active' => true],
            ['name' => 'ESCUELAS VARONES 14 a 17', 'type' => 'escuelas', 'gender' => 'varones', 'age_min' => 14, 'age_max' => 17, 'description' => 'ESCUELAS VARONES 14 a 17', 'active' => true],
            
            // JUNIOR
            ['name' => 'JUNIOR DAMAS', 'type' => 'junior', 'gender' => 'mujeres', 'age_min' => 15, 'age_max' => 18, 'description' => 'JUNIOR DAMAS', 'active' => true],
            ['name' => 'JUNIOR DAMAS 8', 'type' => 'junior', 'gender' => 'mujeres', 'age_min' => 15, 'age_max' => 18, 'description' => 'JUNIOR DAMAS 8', 'active' => true],
            ['name' => 'JUNIOR VARONES', 'type' => 'junior', 'gender' => 'varones', 'age_min' => 15, 'age_max' => 18, 'description' => 'JUNIOR VARONES', 'active' => true],
            ['name' => 'JUNIOR VARONES 9', 'type' => 'junior', 'gender' => 'varones', 'age_min' => 15, 'age_max' => 18, 'description' => 'JUNIOR VARONES 9', 'active' => true],
            
            // MAMAS SIN FRENO
            ['name' => 'MAMAS SIN FRENO', 'type' => 'especial', 'gender' => 'mujeres', 'age_min' => 25, 'age_max' => null, 'description' => 'MAMAS SIN FRENO', 'active' => true],
            
            // NOVICIOS
            ['name' => 'NOVICIOS 6 y -', 'type' => 'novicios', 'gender' => null, 'age_min' => null, 'age_max' => 6, 'description' => 'NOVICIOS 6 y -', 'active' => true],
            ['name' => 'NOVICIOS 7', 'type' => 'novicios', 'gender' => null, 'age_min' => 7, 'age_max' => 7, 'description' => 'NOVICIOS 7', 'active' => true],
            ['name' => 'NOVICIOS 8', 'type' => 'novicios', 'gender' => null, 'age_min' => 8, 'age_max' => 8, 'description' => 'NOVICIOS 8', 'active' => true],
            ['name' => 'NOVICIOS 9', 'type' => 'novicios', 'gender' => null, 'age_min' => 9, 'age_max' => 9, 'description' => 'NOVICIOS 9', 'active' => true],
            ['name' => 'NOVICIOS 10', 'type' => 'novicios', 'gender' => null, 'age_min' => 10, 'age_max' => 10, 'description' => 'NOVICIOS 10', 'active' => true],
            ['name' => 'NOVICIOS 11 y 12', 'type' => 'novicios', 'gender' => null, 'age_min' => 11, 'age_max' => 12, 'description' => 'NOVICIOS 11 y 12', 'active' => true],
            ['name' => 'NOVICIOS 13 y 14', 'type' => 'novicios', 'gender' => null, 'age_min' => 13, 'age_max' => 14, 'description' => 'NOVICIOS 13 y 14', 'active' => true],
            ['name' => 'NOVICIOS 15 y 16', 'type' => 'novicios', 'gender' => null, 'age_min' => 15, 'age_max' => 16, 'description' => 'NOVICIOS 15 y 16', 'active' => true],
            ['name' => 'NOVICIOS 17 a 24', 'type' => 'novicios', 'gender' => null, 'age_min' => 17, 'age_max' => 24, 'description' => 'NOVICIOS 17 a 24', 'active' => true],
            
            // SUB23
            ['name' => 'SUB23 DAMAS', 'type' => 'sub23', 'gender' => 'mujeres', 'age_min' => 19, 'age_max' => 22, 'description' => 'SUB23 DAMAS', 'active' => true],
            ['name' => 'SUB23 VARONES', 'type' => 'sub23', 'gender' => 'varones', 'age_min' => 19, 'age_max' => 22, 'description' => 'SUB23 VARONES', 'active' => true],
            
            // VARONES
            ['name' => 'VARONES 6 y -', 'type' => 'varones', 'gender' => 'varones', 'age_min' => null, 'age_max' => 6, 'description' => 'VARONES 6 y -', 'active' => true],
            ['name' => 'VARONES 7', 'type' => 'varones', 'gender' => 'varones', 'age_min' => 7, 'age_max' => 7, 'description' => 'VARONES 7', 'active' => true],
            ['name' => 'VARONES 8', 'type' => 'varones', 'gender' => 'varones', 'age_min' => 8, 'age_max' => 8, 'description' => 'VARONES 8', 'active' => true],
            ['name' => 'VARONES 9', 'type' => 'varones', 'gender' => 'varones', 'age_min' => 9, 'age_max' => 9, 'description' => 'VARONES 9', 'active' => true],
            ['name' => 'VARONES 10', 'type' => 'varones', 'gender' => 'varones', 'age_min' => 10, 'age_max' => 10, 'description' => 'VARONES 10', 'active' => true],
            ['name' => 'VARONES 11 y 12', 'type' => 'varones', 'gender' => 'varones', 'age_min' => 11, 'age_max' => 12, 'description' => 'VARONES 11 y 12', 'active' => true],
            ['name' => 'VARONES 13 y 14', 'type' => 'varones', 'gender' => 'varones', 'age_min' => 13, 'age_max' => 14, 'description' => 'VARONES 13 y 14', 'active' => true],
            ['name' => 'VARONES 15 y 16', 'type' => 'varones', 'gender' => 'varones', 'age_min' => 15, 'age_max' => 16, 'description' => 'VARONES 15 y 16', 'active' => true],
            ['name' => 'VARONES 17 y +', 'type' => 'varones', 'gender' => 'varones', 'age_min' => 17, 'age_max' => null, 'description' => 'VARONES 17 y +', 'active' => true]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Categorías creadas exitosamente. Total: ' . count($categories));
    }
}
