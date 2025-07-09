<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Limpiar tabla antes de insertar
        Club::truncate();

        $clubs = [
            ['name' => 'AMBMX', 'slug' => 'ambmx', 'status' => 'active'],
            ['name' => 'BICICROSS CHILLAN', 'slug' => 'bicicross-chillan', 'status' => 'active'],
            ['name' => 'BICICROSS CON CON', 'slug' => 'bicicross-con-con', 'status' => 'active'],
            ['name' => 'BMX CONCHALI', 'slug' => 'bmx-conchali', 'status' => 'active'],
            ['name' => 'CLUB BICICROSS SAN BERNARDO', 'slug' => 'club-bicicross-san-bernardo', 'status' => 'active'],
            ['name' => 'CLUB BLACK', 'slug' => 'club-black', 'status' => 'active'],
            ['name' => 'CLUB SB BIKE RACING', 'slug' => 'club-sb-bike-racing', 'status' => 'active'],
            ['name' => 'CLUB TEAM 220', 'slug' => 'club-team-220', 'status' => 'active'],
            ['name' => 'CLUB VINA DEL MAR', 'slug' => 'club-vina-del-mar', 'status' => 'active'],
            ['name' => 'CONDORES DE MACUL', 'slug' => 'condores-de-macul', 'status' => 'active'],
            ['name' => 'LA FLORIDA', 'slug' => 'la-florida', 'status' => 'active'],
            ['name' => 'LA PINTANA', 'slug' => 'la-pintana', 'status' => 'active'],
            ['name' => 'LAS CONDES', 'slug' => 'las-condes', 'status' => 'active'],
            ['name' => 'MAIPU SOBRE RUEDAS', 'slug' => 'maipu-sobre-ruedas', 'status' => 'active'],
            ['name' => 'PENALOLEN BMX RACING', 'slug' => 'penalolen-bmx-racing', 'status' => 'active'],
            ['name' => 'SAN ANTONIO BICICROSS', 'slug' => 'san-antonio-bicicross', 'status' => 'active'],
            ['name' => 'SAN ANTONIO BICICROSS 2', 'slug' => 'san-antonio-bicicross-2', 'status' => 'active'],
            ['name' => 'SANTO DOMINGO', 'slug' => 'santo-domingo', 'status' => 'active'],
            ['name' => 'TEAM VINA RIDE', 'slug' => 'team-vina-ride', 'status' => 'active'],
            ['name' => 'TEMUCO BMX', 'slug' => 'temuco-bmx', 'status' => 'active'],
            ['name' => 'UNION CICLISTAS DE CHILE', 'slug' => 'union-ciclistas-de-chile', 'status' => 'active'],
            ['name' => 'NUNOA', 'slug' => 'nunoa', 'status' => 'active'],
        ];

        foreach ($clubs as $club) {
            Club::create($club);
        }

        $this->command->info('Clubes creados exitosamente. Total: ' . count($clubs));
    }
}
