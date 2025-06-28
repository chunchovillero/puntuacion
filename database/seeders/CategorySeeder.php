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
            // Categoría Escuela Varones (6-12 años)
            [
                'name' => 'Escuela Varones',
                'type' => 'escuela',
                'gender' => 'varones',
                'age_min' => 6,
                'age_max' => 12,
                'description' => 'Categoría para pilotos varones principiantes de 6 a 12 años',
                'active' => true
            ],
            
            // Categoría Escuela Mujeres (6-12 años)
            [
                'name' => 'Escuela Mujeres',
                'type' => 'escuela',
                'gender' => 'mujeres',
                'age_min' => 6,
                'age_max' => 12,
                'description' => 'Categoría para pilotos mujeres principiantes de 6 a 12 años',
                'active' => true
            ],
            
            // Categoría Novicios (6-12 años, sin género)
            [
                'name' => 'Novicios',
                'type' => 'novicios',
                'gender' => null,
                'age_min' => 6,
                'age_max' => 12,
                'description' => 'Categoría para pilotos novicios de 6 a 12 años, sin distinción de género',
                'active' => true
            ],
            
            // Categorías adicionales para mayor flexibilidad
            [
                'name' => 'Juvenil Varones',
                'type' => 'juvenil',
                'gender' => 'varones',
                'age_min' => 13,
                'age_max' => 16,
                'description' => 'Categoría para pilotos varones juveniles de 13 a 16 años',
                'active' => true
            ],
            
            [
                'name' => 'Juvenil Mujeres',
                'type' => 'juvenil',
                'gender' => 'mujeres',
                'age_min' => 13,
                'age_max' => 16,
                'description' => 'Categoría para pilotos mujeres juveniles de 13 a 16 años',
                'active' => true
            ],
            
            [
                'name' => 'Adulto Varones',
                'type' => 'adulto',
                'gender' => 'varones',
                'age_min' => 17,
                'age_max' => null,
                'description' => 'Categoría para pilotos varones adultos de 17 años en adelante',
                'active' => true
            ],
            
            [
                'name' => 'Adulto Mujeres',
                'type' => 'adulto',
                'gender' => 'mujeres',
                'age_min' => 17,
                'age_max' => null,
                'description' => 'Categoría para pilotos mujeres adultos de 17 años en adelante',
                'active' => true
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Categorías creadas exitosamente.');
    }
}
