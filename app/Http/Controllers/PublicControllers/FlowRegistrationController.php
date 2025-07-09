<?php

namespace App\Http\Controllers\PublicControllers;

use App\Http\Controllers\Controller;
use App\Models\Matchday;
use App\Models\Pilot;
use App\Models\Category;
use App\Models\MatchdayParticipant;
use App\Models\Payment;
use App\Services\FlowService;
use App\Services\MockFlowService;
use Illuminate\Http\Request;

class FlowRegistrationController extends Controller
{
    private $flowService;

    public function __construct(FlowService $flowService)
    {
        // Usar mock service en desarrollo
        if (app()->environment('local')) {
            $this->flowService = new MockFlowService();
        } else {
            $this->flowService = $flowService;
        }
    }

    /**
     * Mostrar jornadas disponibles para registro público
     */
    public function index()
    {
        $matchdays = Matchday::with(['championship'])
            ->withCount('participants') // Agregar conteo de participantes
            ->where(function($query) {
                // Jornadas con fechas de registro específicas
                $query->where(function($q) {
                    $q->where('registration_start_date', '<=', now())
                      ->where('registration_end_date', '>=', now());
                })
                // O jornadas sin fechas específicas pero próximas y habilitadas para registro público
                ->orWhere(function($q) {
                    $q->whereNull('registration_start_date')
                      ->whereNull('registration_end_date')
                      ->where('public_registration_enabled', true)
                      ->where('date', '>=', now()->toDateString());
                });
            })
            ->where('status', 'scheduled')
            ->orderBy('date')
            ->get();

        return view('public.flow.index', compact('matchdays'));
    }

    /**
     * Mostrar formulario de registro para una jornada específica
     */
    public function show(Matchday $matchday)
    {
        // Verificar que la jornada esté abierta para registro
        if (!$matchday->isRegistrationOpen()) {
            return redirect()->route('public.flow.index')
                           ->with('error', 'El registro para esta jornada no está disponible.');
        }
        
        return view('public.flow.register', compact('matchday'));
    }

    /**
     * Buscar piloto por RUT
     */
    public function searchPilot(Request $request)
    {
        $request->validate([
            'rut' => 'required|string',
            'matchday_id' => 'required|exists:matchdays,id'
        ]);

        $rut = $this->formatRut($request->rut);
        $matchday = Matchday::findOrFail($request->matchday_id);
        
        // Buscar piloto que esté registrado en el campeonato de esta jornada
        $pilot = Pilot::where('rut', $rut)
                     ->whereHas('championshipRegistrations', function($query) use ($matchday) {
                         $query->where('championship_id', $matchday->championship_id)
                               ->where('status', 'active');
                     })
                     ->with(['club', 'championshipRegistrations' => function($query) use ($matchday) {
                         $query->where('championship_id', $matchday->championship_id)
                               ->with('category');
                     }])
                     ->first();

        if (!$pilot) {
            return response()->json([
                'success' => false,
                'message' => 'Piloto no encontrado o no está registrado en este campeonato.'
            ]);
        }

        // Verificar si el piloto ya está registrado en esta jornada
        $existingParticipant = MatchdayParticipant::where('matchday_id', $matchday->id)
                                                ->where('pilot_id', $pilot->id)
                                                ->first();

        if ($existingParticipant) {
            return response()->json([
                'success' => false,
                'message' => 'El piloto ya está registrado en esta jornada.',
                'already_registered' => true,
                'pilot_info' => [
                    'full_name' => $pilot->full_name,
                    'club_name' => $pilot->club->name ?? 'Sin club'
                ]
            ]);
        }

        $championshipRegistration = $pilot->championshipRegistrations->first();

        return response()->json([
            'success' => true,
            'pilot' => [
                'id' => $pilot->id,
                'full_name' => $pilot->full_name,
                'email' => $pilot->email,
                'phone' => $pilot->phone,
                'club_name' => $pilot->club->name ?? 'Sin club',
                'category_id' => $championshipRegistration->category_id,
                'category_name' => $championshipRegistration->category->name,
                'bib_number' => $championshipRegistration->bib_number
            ]
        ]);
    }

    /**
     * Procesar el registro y crear transacción de pago con Flow
     */
    public function register(Request $request, Matchday $matchday)
    {
        $request->validate([
            'pilot_id' => 'required|exists:pilots,id',
            'payer_name' => 'required|string|max:100',
            'payer_email' => 'required|email|max:100',
        ]);

        // Verificar que la jornada esté abierta
        if (!$matchday->isRegistrationOpen()) {
            return back()->with('error', 'El registro para esta jornada no está disponible.');
        }

        $pilot = Pilot::findOrFail($request->pilot_id);
        
        // Obtener el registro del piloto en este campeonato
        $championshipRegistration = $pilot->championshipRegistrations()
                                         ->where('championship_id', $matchday->championship_id)
                                         ->where('status', 'active')
                                         ->first();
                                         
        if (!$championshipRegistration) {
            return back()->with('error', 'El piloto no está registrado en este campeonato.');
        }

        // Verificar que el piloto no esté ya registrado
        $existingParticipant = MatchdayParticipant::where('matchday_id', $matchday->id)
                                                ->where('pilot_id', $pilot->id)
                                                ->first();

        if ($existingParticipant) {
            return back()->with('error', 'El piloto ya está registrado en esta jornada.');
        }

        // Crear participante usando la categoría del registro del campeonato
        $participant = MatchdayParticipant::create([
            'matchday_id' => $matchday->id,
            'pilot_id' => $pilot->id,
            'category_id' => $championshipRegistration->category_id,
            'registration_number' => $this->generateRegistrationNumber($matchday),
            'entry_fee_paid' => $matchday->entry_fee ?? 5000, // Fee por defecto
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        // Crear registro de pago
        $orderId = Payment::generateOrderId();
        $amount = $matchday->entry_fee ?? 5000;

        $payment = Payment::create([
            'matchday_participant_id' => $participant->id,
            'transaction_id' => '', // Se completará después de Flow
            'order_id' => $orderId,
            'amount' => $amount,
            'currency' => 'CLP',
            'status' => 'pending',
            'payment_method' => 'flow',
            'payer_email' => $request->payer_email,
            'payer_name' => $request->payer_name,
        ]);

        // Crear transacción en Flow
        try {
            $returnUrl = route('public.flow.return', ['payment' => $payment->id]);

            $flowResult = $this->flowService->createPayment(
                $orderId,
                $amount,
                $request->payer_email,
                $returnUrl
            );

            if ($flowResult['success']) {
                // Guardar información de Flow
                $payment->update([
                    'transaction_id' => $flowResult['token'],
                    'webpay_response' => [
                        'flow_token' => $flowResult['token'],
                        'flow_order' => $flowResult['flowOrder'],
                        'payment_url' => $flowResult['url']
                    ]
                ]);

                // Redirigir a Flow
                return redirect($flowResult['url']);
            } else {
                throw new \Exception($flowResult['error']);
            }

        } catch (\Exception $e) {
            // Si falla la creación de la transacción, eliminar participante y pago
            $participant->delete();
            $payment->delete();

            return back()->with('error', 'Error al procesar el pago con Flow: ' . $e->getMessage());
        }
    }

    /**
     * Manejar el retorno desde Flow
     */
    public function paymentReturn(Request $request, Payment $payment)
    {
        try {
            $token = $request->token ?? $request->session()->get('token');
            
            if (!$token) {
                throw new \Exception('Token de Flow no encontrado');
            }

            // Para mock payments en desarrollo
            if (app()->environment('local') && $request->session()->has('mock_action')) {
                $mockAction = $request->session()->get('mock_action');
                
                if ($mockAction === 'approve') {
                    $payment->markAsApproved('MOCK_' . time(), 'APPROVED');
                    $payment->matchdayParticipant->update(['status' => 'confirmed']);
                    return view('public.payment.success', compact('payment'));
                } else {
                    $payment->markAsRejected('REJECTED');
                    return view('public.payment.failed', compact('payment'));
                }
            }

            // Obtener el estado del pago desde Flow
            $statusResult = $this->flowService->getPaymentStatus($token);

            if (!$statusResult['success']) {
                throw new \Exception($statusResult['error']);
            }

            $flowData = $statusResult['data'];

            // Actualizar información del pago
            $payment->update([
                'webpay_response' => array_merge($payment->webpay_response ?? [], [
                    'flow_response' => $flowData
                ])
            ]);

            // Verificar estado de la transacción
            if (isset($flowData['status']) && $flowData['status'] == 2) { // 2 = Pagado en Flow
                $payment->markAsApproved($flowData['flowOrder'], $flowData['status']);
                
                // Actualizar estado del participante
                $payment->matchdayParticipant->update(['status' => 'confirmed']);

                return view('public.payment.success', compact('payment'));
            } else {
                $payment->markAsRejected($flowData['status'] ?? 'ERROR');
                
                return view('public.payment.failed', compact('payment'));
            }

        } catch (\Exception $e) {
            $payment->markAsRejected('ERROR');
            $payment->update(['notes' => 'Error en el retorno de Flow: ' . $e->getMessage()]);

            return view('public.payment.error', [
                'payment' => $payment,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Página de pago simulada para desarrollo
     */
    public function mockPaymentPage(Request $request)
    {
        $token = $request->get('token');
        $order = $request->get('order');
        $amount = $request->get('amount');
        
        return view('public.flow.mock-payment', compact('token', 'order', 'amount'));
    }

    /**
     * Procesar pago simulado
     */
    public function processMockPayment(Request $request)
    {
        $token = $request->get('token');
        $action = $request->get('action'); // 'approve' or 'reject'
        
        // Encontrar el pago por token
        $payment = Payment::whereJsonContains('webpay_response->flow_token', $token)->first();
        
        if (!$payment) {
            return redirect()->route('public.flow.index')->with('error', 'Pago no encontrado');
        }
        
        // Simular el retorno con el token
        return redirect()->route('public.flow.return', ['payment' => $payment->id])
                        ->with('mock_action', $action)
                        ->with('token', $token);
    }

    /**
     * Generar número de registro único para la jornada
     */
    private function generateRegistrationNumber(Matchday $matchday)
    {
        $lastParticipant = MatchdayParticipant::where('matchday_id', $matchday->id)
                                            ->orderBy('registration_number', 'desc')
                                            ->first();

        return $lastParticipant ? $lastParticipant->registration_number + 1 : 1;
    }

    /**
     * Formatear RUT chileno
     */
    private function formatRut($rut)
    {
        $rut = str_replace(['.', ' '], '', $rut);
        $rut = strtoupper($rut);
        if (strpos($rut, '-') === false && strlen($rut) >= 8) {
            $rut = substr($rut, 0, -1) . '-' . substr($rut, -1);
        }
        return $rut;
    }
}
