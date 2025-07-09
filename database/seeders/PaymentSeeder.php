<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\MatchdayParticipant;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener algunos participantes de jornadas
        $participants = MatchdayParticipant::take(10)->get();
        
        if ($participants->count() < 3) {
            $this->command->error('Necesitas al menos 3 participantes de jornadas. Ejecuta TestMatchdayParticipantsSeeder primero.');
            return;
        }

        $paymentMethods = ['flow', 'transferencia', 'efectivo'];
        $statuses = ['approved', 'pending', 'rejected'];
        $amounts = [5000, 7500, 10000];

        foreach ($participants as $index => $participant) {
            // Crear un pago para cada participante
            $payment = Payment::create([
                'matchday_participant_id' => $participant->id,
                'transaction_id' => 'TRX' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'order_id' => 'ORD' . str_pad($index + 1, 8, '0', STR_PAD_LEFT),
                'amount' => $amounts[array_rand($amounts)],
                'currency' => 'CLP',
                'status' => $statuses[array_rand($statuses)],
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payer_email' => $participant->pilot->email ?? 'test@example.com',
                'payer_name' => $participant->pilot->full_name,
                'webpay_response' => [
                    'mock_data' => true,
                    'created_at' => now()->format('Y-m-d H:i:s')
                ],
                'notes' => 'Pago de prueba generado por seeder',
                'created_at' => now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
                'updated_at' => now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
            ]);

            $this->command->info('Pago creado: ' . $payment->order_id . ' - ' . $participant->pilot->full_name);
        }

        // Crear algunos pagos adicionales con diferentes estados
        $extraParticipants = MatchdayParticipant::skip(10)->take(5)->get();
        
        foreach ($extraParticipants as $index => $participant) {
            // Crear pagos con diferentes estados para mostrar variedad
            $statuses = ['approved', 'approved', 'pending', 'rejected', 'failed'];
            $status = $statuses[$index % count($statuses)];
            
            Payment::create([
                'matchday_participant_id' => $participant->id,
                'transaction_id' => $status === 'approved' ? 'TRX' . str_pad($index + 100, 6, '0', STR_PAD_LEFT) : null,
                'order_id' => 'ORD' . str_pad($index + 100, 8, '0', STR_PAD_LEFT),
                'amount' => 5000,
                'currency' => 'CLP',
                'status' => $status,
                'payment_method' => 'flow',
                'payer_email' => $participant->pilot->email ?? 'test@example.com',
                'payer_name' => $participant->pilot->full_name,
                'webpay_response' => [
                    'status' => $status,
                    'mock_data' => true
                ],
                'notes' => 'Pago de prueba - Estado: ' . $status,
                'created_at' => now()->subDays(rand(0, 3))->subHours(rand(0, 23)),
                'updated_at' => now()->subDays(rand(0, 3))->subHours(rand(0, 23)),
            ]);
        }

        $this->command->info('Pagos de prueba creados exitosamente!');
    }
}
