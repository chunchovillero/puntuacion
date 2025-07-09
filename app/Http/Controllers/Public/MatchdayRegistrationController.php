<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Matchday;
use App\Models\Pilot;
use App\Models\Category;
use App\Models\MatchdayParticipant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Transbank\Webpay\WebpayPlus\Transaction;
use Transbank\Webpay\WebpayPlus;

class MatchdayRegistrationController extends Controller
{
    public function __construct()
    {
        // Configurar WebPay para desarrollo
        WebpayPlus::configureForIntegration('597055555532', '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C');
    }

    /**
     * Mostrar jornadas disponibles para registro público
     */
    public function index()
    {
        $matchdays = Matchday::with(['championship'])
            ->where('registration_start_date', '<=', now())
            ->where('registration_end_date', '>=', now())
            ->where('status', 'upcoming')
            ->orderBy('event_date')
            ->get();

        return view('public.matchdays.index', compact('matchdays'));
    }

    /**
     * Mostrar formulario de registro para una jornada específica
     */
    public function show(Matchday $matchday)
    {
        // Verificar que la jornada esté abierta para registro
        if (!$matchday->isRegistrationOpen()) {
            return redirect()->route('public.matchdays.index')
                           ->with('error', 'El registro para esta jornada no está disponible.');
        }

        $categories = Category::active()->orderBy('type')->orderBy('name')->get();
        
        return view('public.matchdays.register', compact('matchday', 'categories'));
    }

    /**
     * Buscar piloto por RUT
     */
    public function searchPilot(Request $request)
    {
        $request->validate([
            'rut' => 'required|string'
        ]);

        $rut = $this->formatRut($request->rut);
        $pilot = Pilot::where('rut', $rut)->first();

        if (!$pilot) {
            return response()->json([
                'success' => false,
                'message' => 'Piloto no encontrado. Debe estar registrado en el sistema previamente.'
            ]);
        }

        return response()->json([
            'success' => true,
            'pilot' => [
                'id' => $pilot->id,
                'full_name' => $pilot->full_name,
                'email' => $pilot->email,
                'phone' => $pilot->phone,
                'club_name' => $pilot->club->name ?? 'Sin club'
            ]
        ]);
    }

    /**
     * Procesar el registro y crear transacción de pago
     */
    public function register(Request $request, Matchday $matchday)
    {
        $request->validate([
            'pilot_id' => 'required|exists:pilots,id',
            'category_id' => 'required|exists:categories,id',
            'payer_name' => 'required|string|max:100',
            'payer_email' => 'required|email|max:100',
        ]);

        // Verificar que la jornada esté abierta
        if (!$matchday->isRegistrationOpen()) {
            return back()->with('error', 'El registro para esta jornada no está disponible.');
        }

        $pilot = Pilot::findOrFail($request->pilot_id);
        $category = Category::findOrFail($request->category_id);

        // Verificar que el piloto no esté ya registrado
        $existingParticipant = MatchdayParticipant::where('matchday_id', $matchday->id)
                                                ->where('pilot_id', $pilot->id)
                                                ->first();

        if ($existingParticipant) {
            return back()->with('error', 'El piloto ya está registrado en esta jornada.');
        }

        // Crear participante
        $participant = MatchdayParticipant::create([
            'matchday_id' => $matchday->id,
            'pilot_id' => $pilot->id,
            'category_id' => $category->id,
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
            'transaction_id' => '', // Se completará después de WebPay
            'order_id' => $orderId,
            'amount' => $amount,
            'currency' => 'CLP',
            'status' => 'pending',
            'payment_method' => 'webpay',
            'payer_email' => $request->payer_email,
            'payer_name' => $request->payer_name,
        ]);

        // Crear transacción en WebPay
        try {
            $transaction = new Transaction();
            $response = $transaction->create(
                $orderId,
                session()->getId(), // session_id
                $amount,
                route('public.payment.return', ['payment' => $payment->id])
            );

            // Guardar token de transacción
            $payment->update([
                'transaction_id' => $response->getToken(),
                'webpay_response' => [
                    'token' => $response->getToken(),
                    'url' => $response->getUrl()
                ]
            ]);

            // Redirigir a WebPay
            return redirect($response->getUrl() . '?token_ws=' . $response->getToken());

        } catch (\Exception $e) {
            // Si falla la creación de la transacción, eliminar participante y pago
            $participant->delete();
            $payment->delete();

            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Manejar el retorno desde WebPay
     */
    public function paymentReturn(Request $request, Payment $payment)
    {
        try {
            $token = $request->token_ws;
            
            if (!$token) {
                throw new \Exception('Token de transacción no encontrado');
            }

            $transaction = new Transaction();
            $response = $transaction->commit($token);

            // Actualizar información del pago
            $payment->update([
                'webpay_response' => array_merge($payment->webpay_response ?? [], [
                    'commit_response' => [
                        'vci' => $response->getVci(),
                        'amount' => $response->getAmount(),
                        'status' => $response->getStatus(),
                        'buy_order' => $response->getBuyOrder(),
                        'session_id' => $response->getSessionId(),
                        'card_detail' => $response->getCardDetail(),
                        'accounting_date' => $response->getAccountingDate(),
                        'transaction_date' => $response->getTransactionDate(),
                        'authorization_code' => $response->getAuthorizationCode(),
                        'payment_type_code' => $response->getPaymentTypeCode(),
                        'response_code' => $response->getResponseCode(),
                        'installments_amount' => $response->getInstallmentsAmount(),
                        'installments_number' => $response->getInstallmentsNumber(),
                        'balance' => $response->getBalance(),
                    ]
                ])
            ]);

            // Verificar estado de la transacción
            if ($response->getStatus() === 'AUTHORIZED') {
                $payment->markAsApproved($response->getAuthorizationCode(), $response->getResponseCode());
                
                // Actualizar estado del participante
                $payment->matchdayParticipant->update(['status' => 'confirmed']);

                return view('public.payment.success', compact('payment'));
            } else {
                $payment->markAsRejected($response->getResponseCode());
                
                return view('public.payment.failed', compact('payment'));
            }

        } catch (\Exception $e) {
            $payment->markAsRejected('ERROR');
            $payment->update(['notes' => 'Error en el retorno: ' . $e->getMessage()]);

            return view('public.payment.error', [
                'payment' => $payment,
                'error' => $e->getMessage()
            ]);
        }
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
