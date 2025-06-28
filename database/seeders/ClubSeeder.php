<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\Pilot;
use App\Models\Category;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $clubs = [
            [
                'name' => 'BMX Riders Mexico',
                'description' => 'Club pionero del BMX en Mexico, dedicado a promover este deporte entre jovenes y adultos.',
                'address' => 'Av. Insurgentes Sur 1234',
                'city' => 'Ciudad de Mexico',
                'state' => 'CDMX',
                'country' => 'Mexico',
                'postal_code' => '03100',
                'phone' => '+52 55 1234 5678',
                'email' => 'info@bmxridersmexico.com',
                'website' => 'https://bmxridersmexico.com',
                'facebook' => 'https://facebook.com/bmxridersmexico',
                'instagram' => 'https://instagram.com/bmxridersmexico',
                'founded_date' => '2015-03-15',
                'status' => 'active',
            ],
            [
                'name' => 'Guadalajara BMX Club',
                'description' => 'Club especializado en competencias de BMX race y freestyle en la zona metropolitana de Guadalajara.',
                'address' => 'Calle Revolucion 567',
                'city' => 'Guadalajara',
                'state' => 'Jalisco',
                'country' => 'Mexico',
                'postal_code' => '44100',
                'phone' => '+52 33 2345 6789',
                'email' => 'contacto@gdlbmx.mx',
                'website' => 'https://guadalajarabmx.mx',
                'instagram' => 'https://instagram.com/gdlbmxclub',
                'founded_date' => '2018-08-20',
                'status' => 'active',
            ],
            [
                'name' => 'Monterrey Extreme BMX',
                'description' => 'Grupo de entusiastas del BMX extreme y dirt jumping en el area metropolitana de Monterrey.',
                'address' => 'Av. Constitucion 890',
                'city' => 'Monterrey',
                'state' => 'Nuevo Leon',
                'country' => 'Mexico',
                'postal_code' => '64000',
                'phone' => '+52 81 3456 7890',
                'email' => 'info@mtyextremebmx.com',
                'website' => 'https://monterreyextremebmx.com',
                'facebook' => 'https://facebook.com/mtyextremebmx',
                'instagram' => 'https://instagram.com/mtyextremebmx',
                'twitter' => 'https://twitter.com/mtyextremebmx',
                'founded_date' => '2017-01-10',
                'status' => 'active',
            ],
            [
                'name' => 'Tijuana BMX Academy',
                'description' => 'Academia de BMX enfocada en la formacion de nuevos talentos y competidores profesionales.',
                'address' => 'Blvd. Agua Caliente 456',
                'city' => 'Tijuana',
                'state' => 'Baja California',
                'country' => 'Mexico',
                'postal_code' => '22000',
                'phone' => '+52 664 4567 8901',
                'email' => 'academy@tijuanabmx.org',
                'website' => 'https://tijuanabmxacademy.org',
                'founded_date' => '2019-05-30',
                'status' => 'active',
            ],
            [
                'name' => 'Puebla BMX Riders',
                'description' => 'Comunidad de riders de BMX en Puebla, enfocados en street y park riding.',
                'address' => 'Calle 16 de Septiembre 123',
                'city' => 'Puebla',
                'state' => 'Puebla',
                'country' => 'Mexico',
                'postal_code' => '72000',
                'phone' => '+52 222 5678 9012',
                'email' => 'info@pueblabmx.mx',
                'instagram' => 'https://instagram.com/pueblabmxriders',
                'founded_date' => '2020-02-14',
                'status' => 'active',
            ]
        ];

        foreach ($clubs as $clubData) {
            $club = Club::updateOrCreate(
                ['name' => $clubData['name']], // Buscar por nombre
                $clubData // Datos a crear o actualizar
            );
            
            // Crear pilotos de ejemplo para cada club solo si no tiene pilotos
            if ($club->pilots()->count() == 0) {
                $this->createPilotsForClub($club);
            }
        }
    }

    private function createPilotsForClub(Club $club)
    {
        $pilots = [
            [
                'first_name' => 'Carlos',
                'last_name' => 'Rodriguez',
                'nickname' => 'El Rayo',
                'description' => 'Especialista en BMX race con multiples titulos nacionales.',
                'birth_date' => '1995-06-15',
                'gender' => 'male',
                'phone' => '+52 55 1111 2222',
                'email' => 'carlos.rodriguez@email.com',
                'bike_brand' => 'Haro',
                'bike_model' => 'Race Lite Pro',
                'bike_year' => 2023,
                'category_id' => null, // Se asignará después
                'specialties' => ['race', 'dirt'],
                'status' => 'active',
                'ranking_points' => 850,
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Gonzalez',
                'nickname' => 'La Flecha',
                'description' => 'Rider de freestyle con estilo unico en park y street.',
                'birth_date' => '1998-11-22',
                'gender' => 'female',
                'phone' => '+52 55 3333 4444',
                'email' => 'maria.gonzalez@email.com',
                'bike_brand' => 'Cult',
                'bike_model' => 'Gateway',
                'bike_year' => 2022,
                'category_id' => null, // Se asignará después
                'specialties' => ['freestyle', 'park', 'street'],
                'status' => 'active',
                'ranking_points' => 720,
            ],
            [
                'first_name' => 'Juan',
                'last_name' => 'Perez',
                'nickname' => null,
                'description' => 'Piloto junior en desarrollo, gran promesa del BMX mexicano.',
                'birth_date' => '2005-03-08',
                'gender' => 'male',
                'phone' => '+52 55 5555 6666',
                'email' => 'juan.perez@email.com',
                'bike_brand' => 'Mongoose',
                'bike_model' => 'Title Elite Pro',
                'bike_year' => 2023,
                'category_id' => null, // Se asignará después
                'specialties' => ['race'],
                'status' => 'active',
                'ranking_points' => 450,
            ]
        ];

        foreach ($pilots as $pilotData) {
            $pilotData['club_id'] = $club->id;
            $pilot = Pilot::create($pilotData);
            
            // Asignar categoría apropiada basada en edad y género
            $pilot->assignAppropriateCategory();
        }

        // Actualizar contador de pilotos del club
        $club->updatePilotCount();
    }
}
