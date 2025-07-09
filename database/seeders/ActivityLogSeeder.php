<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Pilot;
use App\Models\Championship;
use App\Models\Matchday;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->info('No hay usuarios. Creando usuario de prueba...');
            $user = User::create([
                'name' => 'Admin Test',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        // Log de creación de piloto
        $pilot = Pilot::first();
        if ($pilot) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'create',
                'model_type' => 'App\Models\Pilot',
                'model_id' => $pilot->id,
                'model_name' => $pilot->full_name,
                'new_values' => [
                    'first_name' => $pilot->first_name,
                    'last_name' => $pilot->last_name,
                    'rut' => $pilot->rut,
                    'club_id' => $pilot->club_id,
                ],
                'description' => 'Creó Piloto: ' . $pilot->full_name,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(2),
            ]);

            // Log de edición de piloto
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'update',
                'model_type' => 'App\Models\Pilot',
                'model_id' => $pilot->id,
                'model_name' => $pilot->full_name,
                'old_values' => [
                    'phone' => null,
                ],
                'new_values' => [
                    'phone' => '+56912345678',
                ],
                'description' => 'Editó Piloto: ' . $pilot->full_name,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(1),
            ]);
        }

        // Log de creación de campeonato
        $championship = Championship::first();
        if ($championship) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'create',
                'model_type' => 'App\Models\Championship',
                'model_id' => $championship->id,
                'model_name' => $championship->name,
                'new_values' => [
                    'name' => $championship->name,
                    'year' => $championship->year,
                    'status' => $championship->status,
                ],
                'description' => 'Creó Campeonato: ' . $championship->name,
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(5),
            ]);
        }

        // Log de creación de jornada
        $matchday = Matchday::first();
        if ($matchday) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'create',
                'model_type' => 'App\Models\Matchday',
                'model_id' => $matchday->id,
                'model_name' => $matchday->name,
                'new_values' => [
                    'name' => $matchday->name,
                    'championship_id' => $matchday->championship_id,
                    'date' => $matchday->date,
                    'location' => $matchday->location,
                ],
                'description' => 'Creó Jornada: ' . $matchday->name,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15',
                'created_at' => now()->subDays(3),
            ]);

            // Log de edición de jornada
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'update',
                'model_type' => 'App\Models\Matchday',
                'model_id' => $matchday->id,
                'model_name' => $matchday->name,
                'old_values' => [
                    'entry_fee' => null,
                ],
                'new_values' => [
                    'entry_fee' => 5000,
                ],
                'description' => 'Editó Jornada: ' . $matchday->name,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subHours(12),
            ]);
        }

        // Log de login del usuario (simulado)
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'model_name' => $user->name,
            'description' => 'Usuario inició sesión',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'created_at' => now()->subHours(2),
        ]);

        // Logs recientes de sistema
        ActivityLog::create([
            'user_id' => null,
            'action' => 'system',
            'model_type' => 'System',
            'model_id' => null,
            'model_name' => 'Database Backup',
            'description' => 'Respaldo automático de base de datos realizado',
            'ip_address' => null,
            'user_agent' => 'System/Cron',
            'created_at' => now()->subMinutes(30),
        ]);

        $this->command->info('Logs de actividad de prueba creados exitosamente!');
    }
}
