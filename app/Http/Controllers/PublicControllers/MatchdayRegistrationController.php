<?php

namespace App\Http\Controllers\PublicControllers;

use App\Http\Controllers\Controller;
use App\Models\Matchday;
use App\Models\Pilot;
use App\Models\Category;
use App\Models\MatchdayParticipant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Transbank\Webpay\WebpayPlus\Transaction;
use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\Options as TransbankOptions;
use Transbank\Webpay\Options;
use GuzzleHttp\Client;

class MatchdayRegistrationController extends Controller
{
    public function __construct()
    {
        // Configurar WebPay para desarrollo
        WebpayPlus::configureForIntegration('597055555532', '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C');
        
        // Configurar SSL para desarrollo local (múltiples capas)
        $this->configureSSLForDevelopment();
        $this->configureCurlForTransbank();
        $this->configureGuzzleForTransbank();
        $this->forceDisableSSLGlobally();
    }

    /**
     * Configurar SSL para desarrollo local
     */
    private function configureSSLForDevelopment()
    {
        if (app()->environment('local', 'development')) {
            // Configurar certificado CA bundle
            $caCertPath = base_path('cacert.pem');
            
            if (file_exists($caCertPath)) {
                // Usar certificado CA bundle descargado
                ini_set('curl.cainfo', $caCertPath);
                ini_set('openssl.cafile', $caCertPath);
            } else {
                // Si no existe el certificado, deshabilitar verificación SSL (solo para desarrollo)
                ini_set('curl.cainfo', '');
                
                // Configurar variables de entorno para cURL
                putenv('CURLOPT_SSL_VERIFYPEER=0');
                putenv('CURLOPT_SSL_VERIFYHOST=0');
                
                // Configurar stream context
                $context = stream_context_create([
                    "ssl" => [
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                    ],
                    "http" => [
                        "timeout" => 60,
                    ]
                ]);
                libxml_set_streams_context($context);
                
                // Configurar opciones por defecto de cURL
                curl_setopt_array(curl_init(), [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]);
            }
        }
    }

    /**
     * Configurar cURL específicamente para Transbank
     */
    private function configureCurlForTransbank()
    {
        if (app()->environment('local', 'development')) {
            // Solución más agresiva: configurar todas las opciones posibles
            
            // 1. Configurar php.ini temporalmente
            $originalCaInfo = ini_get('curl.cainfo');
            $originalOpenSSLCaFile = ini_get('openssl.cafile');
            
            ini_set('curl.cainfo', '');
            ini_set('openssl.cafile', '');
            ini_set('default_socket_timeout', '60');
            
            // 2. Variables de entorno
            putenv('CURLOPT_SSL_VERIFYPEER=0');
            putenv('CURLOPT_SSL_VERIFYHOST=0');
            putenv('CURL_CA_BUNDLE=');
            putenv('SSL_CERT_FILE=');
            
            // 3. Configurar variables globales
            $_ENV['CURL_CA_BUNDLE'] = '';
            $_SERVER['CURL_CA_BUNDLE'] = '';
            $_ENV['SSL_CERT_FILE'] = '';
            $_SERVER['SSL_CERT_FILE'] = '';
            
            // 4. Configurar stream context por defecto
            stream_context_set_default([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                    'disable_compression' => true,
                ],
                'http' => [
                    'timeout' => 60,
                    'ignore_errors' => true,
                ]
            ]);
            
            // 5. Intentar configurar defaults de libcurl si es posible
            if (function_exists('curl_setopt')) {
                $GLOBALS['CURL_DEFAULTS'] = [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_TIMEOUT => 60,
                    CURLOPT_CONNECTTIMEOUT => 30,
                ];
            }
        }
    }

    /**
     * Configurar Guzzle HTTP client para Transbank
     */
    private function configureGuzzleForTransbank()
    {
        if (app()->environment('local', 'development')) {
            // Configurar Guzzle para desarrollo sin SSL
            app()->bind(\GuzzleHttp\Client::class, function () {
                return new \GuzzleHttp\Client([
                    'verify' => false, // Deshabilitar verificación SSL
                    'timeout' => 60,
                    'connect_timeout' => 30,
                    'http_errors' => false, // No lanzar excepciones por errores HTTP
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ]);
            });
        }
    }

    /**
     * Mostrar jornadas disponibles para registro público
     */
    public function index()
    {
        // En desarrollo, redirigir a Flow para evitar problemas de SSL
        if (app()->environment('local', 'development')) {
            return redirect()->route('public.flow.index')
                           ->with('info', 'Redirigido a Flow para evitar problemas de SSL en desarrollo');
        }

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

        return view('public.matchdays.index', compact('matchdays'));
    }

    /**
     * Vue.js index view
     */
    public function vueIndex()
    {
        return view('public.matchdays.vue-index');
    }

    /**
     * API: Get available matchdays for public registration
     */
    public function apiIndex()
    {
        try {
            $matchdays = Matchday::with(['championship'])
                ->withCount('participants')
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
                ->get()
                ->map(function($matchday) {
                    return [
                        'id' => $matchday->id,
                        'name' => $matchday->name,
                        'full_name' => $matchday->full_name,
                        'date' => $matchday->date,
                        'start_time' => $matchday->start_time,
                        'venue' => $matchday->venue,
                        'entry_fee' => $matchday->entry_fee,
                        'participants_count' => $matchday->participants_count,
                        'is_registration_open' => $matchday->isRegistrationOpen(),
                        'championship' => [
                            'id' => $matchday->championship->id,
                            'name' => $matchday->championship->name
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $matchdays
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener jornadas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get participants for a specific matchday
     */
    public function apiParticipants(Matchday $matchday)
    {
        try {
            $participants = MatchdayParticipant::with(['pilot.category', 'pilot.club', 'payment'])
                ->where('matchday_id', $matchday->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($participant) {
                    return [
                        'id' => $participant->id,
                        'pilot' => [
                            'id' => $participant->pilot->id,
                            'first_name' => $participant->pilot->first_name,
                            'last_name' => $participant->pilot->last_name,
                            'rut' => $participant->pilot->rut,
                            'category' => $participant->pilot->category ? [
                                'id' => $participant->pilot->category->id,
                                'name' => $participant->pilot->category->name
                            ] : null,
                            'club' => $participant->pilot->club ? [
                                'id' => $participant->pilot->club->id,
                                'name' => $participant->pilot->club->name
                            ] : null
                        ],
                        'payment_status' => $participant->payment ? $participant->payment->status : 'pending',
                        'created_at' => $participant->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $participants
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener participantes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Search pilots
     */
    public function apiSearchPilot(Request $request)
    {
        try {
            $request->validate([
                'term' => 'required|string|min:3'
            ]);

            $term = $request->term;
            
            $pilots = Pilot::with(['category', 'club'])
                ->where(function($query) use ($term) {
                    $query->where('first_name', 'LIKE', "%{$term}%")
                          ->orWhere('last_name', 'LIKE', "%{$term}%")
                          ->orWhere('rut', 'LIKE', "%{$term}%")
                          ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]);
                })
                ->where('status', 'active')
                ->limit(10)
                ->get()
                ->map(function($pilot) {
                    return [
                        'id' => $pilot->id,
                        'first_name' => $pilot->first_name,
                        'last_name' => $pilot->last_name,
                        'rut' => $pilot->rut,
                        'category' => $pilot->category ? [
                            'id' => $pilot->category->id,
                            'name' => $pilot->category->name
                        ] : null,
                        'club' => $pilot->club ? [
                            'id' => $pilot->club->id,
                            'name' => $pilot->club->name
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $pilots
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * API: Register pilot for matchday
     */
    public function apiRegister(Request $request, Matchday $matchday)
    {
        try {
            // Verificar que la jornada esté abierta para registro
            if (!$matchday->isRegistrationOpen()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El registro para esta jornada no está disponible.'
                ], 422);
            }

            $request->validate([
                'pilot_id' => 'required|exists:pilots,id',
                'observations' => 'nullable|string|max:500'
            ]);

            $pilot = Pilot::findOrFail($request->pilot_id);

            // Verificar que el piloto no esté ya registrado
            $existingParticipant = MatchdayParticipant::where('matchday_id', $matchday->id)
                ->where('pilot_id', $pilot->id)
                ->first();

            if ($existingParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'El piloto ya está registrado en esta jornada.'
                ], 422);
            }

            // Crear participante
            $participant = MatchdayParticipant::create([
                'matchday_id' => $matchday->id,
                'pilot_id' => $pilot->id,
                'category_id' => $pilot->category_id,
                'observations' => $request->observations,
                'registration_date' => now(),
            ]);

            // Crear payment record
            $payment = Payment::create([
                'participant_id' => $participant->id,
                'amount' => $matchday->entry_fee ?? 5000,
                'status' => 'pending',
                'payment_method' => 'webpay_plus'
            ]);

            // En desarrollo, simular pago
            if (app()->environment('local', 'development')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registro completado (modo desarrollo)',
                    'payment_url' => route('public.flow.mock-payment', ['payment' => $payment->id])
                ]);
            }

            // Crear transacción con Transbank (producción)
            $transaction = new Transaction();
            $amount = $payment->amount;
            $sessionId = session()->getId();
            $buyOrder = 'ORDER-' . $payment->id . '-' . time();
            $returnUrl = route('public.payment.return', ['payment' => $payment->id]);

            $response = $transaction->create($buyOrder, $sessionId, $amount, $returnUrl);

            // Actualizar payment con token de transacción
            $payment->update([
                'transaction_token' => $response->getToken(),
                'buy_order' => $buyOrder
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registro iniciado, proceder al pago',
                'payment_url' => $response->getUrl() . '?token_ws=' . $response->getToken()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en el registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar jornada específica (detalles y formulario de registro)
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
            // Configurar SSL de manera extrema antes de la transacción
            $this->forceDisableSSLGlobally();
            $this->configureCurlForTransbank();
            
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

            // Configurar SSL antes de confirmar transacción
            $this->forceDisableSSLGlobally();
            $this->configureCurlForTransbank();

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

    /**
     * Función de último recurso: interceptar y configurar cURL globalmente
     */
    private function forceDisableSSLGlobally()
    {
        if (app()->environment('local', 'development')) {
            // Usar output buffering para capturar y modificar el comportamiento
            ob_start();
            
            // Configurar TODAS las variables posibles
            $sslVars = [
                'CURLOPT_SSL_VERIFYPEER' => '0',
                'CURLOPT_SSL_VERIFYHOST' => '0',
                'CURL_CA_BUNDLE' => '',
                'SSL_CERT_FILE' => '',
                'SSL_CERT_DIR' => '',
                'REQUESTS_CA_BUNDLE' => '',
                'CURL_CA_BUNDLE_PATH' => '',
            ];
            
            foreach ($sslVars as $var => $value) {
                putenv("$var=$value");
                $_ENV[$var] = $value;
                $_SERVER[$var] = $value;
            }
            
            // Configurar ini_set de manera más agresiva
            $iniSettings = [
                'curl.cainfo' => '',
                'openssl.cafile' => '',
                'openssl.capath' => '',
                'auto_detect_line_endings' => 'Off',
                'user_agent' => 'TransbankSDK-PHP/4.0.0',
                'default_socket_timeout' => '60',
            ];
            
            foreach ($iniSettings as $setting => $value) {
                ini_set($setting, $value);
            }
            
            // Modificar superglobales que Guzzle puede estar leyendo
            if (!defined('CURL_SSLVERSION_TLSv1_2')) {
                define('CURL_SSLVERSION_TLSv1_2', 6);
            }
            
            // Configurar el context HTTP por defecto de manera más específica
            $contextOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                    'disable_compression' => true,
                    'SNI_enabled' => false,
                    'ciphers' => 'DEFAULT:!DH',
                ],
                'http' => [
                    'method' => 'POST',
                    'timeout' => 60,
                    'ignore_errors' => true,
                    'user_agent' => 'TransbankSDK-PHP/4.0.0',
                ]
            ];
            
            stream_context_set_default($contextOptions);
            
            $context = stream_context_create($contextOptions);
            libxml_set_streams_context($context);
        }
    }

    /**
     * Mostrar participantes de una jornada (vista pública para invitados)
     */
    public function showParticipants(Matchday $matchday)
    {
        // Obtener participantes con información completa
        $participants = MatchdayParticipant::where('matchday_id', $matchday->id)
            ->with([
                'pilot.club', // Piloto y su club
                'pilot.championshipRegistrations' => function($query) use ($matchday) {
                    $query->where('championship_id', $matchday->championship_id);
                }
            ])
            ->get()
            ->map(function($participant) {
                // Agregar información del registro del campeonato
                $championshipRegistration = $participant->pilot->championshipRegistrations->first();
                $participant->category = $championshipRegistration ? $championshipRegistration->category : null;
                $participant->dorsal = $championshipRegistration ? $championshipRegistration->bib_number : null;
                return $participant;
            })
            ->sortBy('dorsal'); // Ordenar por dorsal

        // Agrupar participantes por categoría
        $participantsByCategory = $participants->groupBy(function($participant) {
            return $participant->category ? $participant->category->name : 'Sin Categoría';
        });

        // Ordenar las categorías por nombre
        $participantsByCategory = $participantsByCategory->sortKeys();

        return view('public.matchdays.participants', compact('matchday', 'participants', 'participantsByCategory'));
    }
}
