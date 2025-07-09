<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = Setting::getAllGrouped();
        
        // Si no hay configuraciones, crear las predeterminadas
        if ($settings->isEmpty()) {
            $this->createDefaultSettings();
            $settings = Setting::getAllGrouped();
        }
        
        $settingsByGroup = $settings;
        
        return view('admin.settings.index', compact('settingsByGroup'));
    }

    /**
     * Display the Vue.js settings page
     */
    public function vueIndex()
    {
        return view('admin.settings.vue-index');
    }

    /**
     * Get settings data for API
     */
    public function apiIndex()
    {
        try {
            $settings = Setting::getAllGrouped();
            
            // Si no hay configuraciones, crear las predeterminadas
            if ($settings->isEmpty()) {
                $this->createDefaultSettings();
                $settings = Setting::getAllGrouped();
            }

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener configuraciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update settings via API
     */
    public function apiUpdate(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'settings' => 'required|array',
                'settings.*' => 'nullable|string|max:1000'
            ]);

            foreach ($validatedData['settings'] as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    // Validar según el tipo de campo
                    $this->validateSettingValue($setting, $value);
                    
                    $setting->update(['value' => $value]);
                }
            }

            // Limpiar cache de configuraciones
            Cache::forget('app_settings');

            return response()->json([
                'success' => true,
                'message' => 'Configuraciones actualizadas exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar configuraciones: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Reset settings to default via API
     */
    public function apiReset()
    {
        try {
            Setting::truncate();
            $this->createDefaultSettings();
            Cache::forget('app_settings');

            return response()->json([
                'success' => true,
                'message' => 'Configuraciones restablecidas a valores predeterminados.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restablecer configuraciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export settings via API
     */
    public function apiExport()
    {
        try {
            $settings = Setting::all();
            $exportData = [];

            foreach ($settings as $setting) {
                $exportData[] = [
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'description' => $setting->description,
                    'group' => $setting->group,
                    'options' => $setting->options
                ];
            }

            $filename = 'configuraciones_' . date('Y-m-d_H-i-s') . '.json';
            
            return response()->json([
                'success' => true,
                'data' => $exportData,
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar configuraciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import settings via API
     */
    public function apiImport(Request $request)
    {
        try {
            $request->validate([
                'import_file' => 'required|file|mimes:json|max:2048'
            ]);

            $content = File::get($request->file('import_file')->path());
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo JSON inválido.'
                ], 422);
            }

            foreach ($data as $settingData) {
                Setting::updateOrCreate(
                    ['key' => $settingData['key']],
                    [
                        'value' => $settingData['value'],
                        'type' => $settingData['type'],
                        'description' => $settingData['description'],
                        'group' => $settingData['group'],
                        'options' => $settingData['options'] ?? null
                    ]
                );
            }

            Cache::forget('app_settings');

            return response()->json([
                'success' => true,
                'message' => 'Configuraciones importadas exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al importar configuraciones: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Validate setting value based on type
     */
    private function validateSettingValue($setting, $value)
    {
        switch ($setting->type) {
            case 'boolean':
                if (!in_array($value, ['0', '1', 'true', 'false'])) {
                    throw new \InvalidArgumentException("Valor inválido para {$setting->key}");
                }
                break;
            case 'integer':
                if (!is_numeric($value) || !is_int((int)$value)) {
                    throw new \InvalidArgumentException("Valor debe ser un entero para {$setting->key}");
                }
                break;
            case 'float':
                if (!is_numeric($value)) {
                    throw new \InvalidArgumentException("Valor debe ser un número para {$setting->key}");
                }
                break;
            case 'json':
                json_decode($value);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException("Valor debe ser JSON válido para {$setting->key}");
                }
                break;
        }
    }

    /**
     * Create default application settings
     */
    public function createDefaultSettings()
    {
        $defaultSettings = [
            // Configuraciones generales
            [
                'key' => 'app_name',
                'value' => 'Sistema BMX Puntuación',
                'type' => 'text',
                'description' => 'Nombre de la aplicación',
                'group' => 'general'
            ],
            [
                'key' => 'app_description',
                'value' => 'Sistema de gestión y puntuación para competencias BMX',
                'type' => 'textarea',
                'description' => 'Descripción de la aplicación',
                'group' => 'general'
            ],
            [
                'key' => 'organization_name',
                'value' => 'Federación BMX Chile',
                'type' => 'text',
                'description' => 'Nombre de la organización',
                'group' => 'general'
            ],
            [
                'key' => 'contact_email',
                'value' => 'contacto@bmxchile.cl',
                'type' => 'email',
                'description' => 'Email de contacto principal',
                'group' => 'general'
            ],
            [
                'key' => 'contact_phone',
                'value' => '+56 9 1234 5678',
                'type' => 'text',
                'description' => 'Teléfono de contacto',
                'group' => 'general'
            ],

            // Sistema
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Activar modo mantenimiento',
                'group' => 'system'
            ],
            [
                'key' => 'max_upload_size',
                'value' => '2048',
                'type' => 'integer',
                'description' => 'Tamaño máximo de archivo (KB)',
                'group' => 'system'
            ],
            [
                'key' => 'session_timeout',
                'value' => '120',
                'type' => 'integer',
                'description' => 'Tiempo de sesión (minutos)',
                'group' => 'system'
            ],

            // Competencias
            [
                'key' => 'default_points_first',
                'value' => '100',
                'type' => 'integer',
                'description' => 'Puntos por primer lugar',
                'group' => 'competition'
            ],
            [
                'key' => 'default_points_second',
                'value' => '80',
                'type' => 'integer',
                'description' => 'Puntos por segundo lugar',
                'group' => 'competition'
            ],
            [
                'key' => 'default_points_third',
                'value' => '60',
                'type' => 'integer',
                'description' => 'Puntos por tercer lugar',
                'group' => 'competition'
            ],
            [
                'key' => 'registration_fee',
                'value' => '5000',
                'type' => 'integer',
                'description' => 'Tarifa de inscripción por defecto (CLP)',
                'group' => 'competition'
            ],

            // Email
            [
                'key' => 'smtp_host',
                'value' => 'smtp.gmail.com',
                'type' => 'text',
                'description' => 'Servidor SMTP',
                'group' => 'email'
            ],
            [
                'key' => 'smtp_port',
                'value' => '587',
                'type' => 'integer',
                'description' => 'Puerto SMTP',
                'group' => 'email'
            ],
            [
                'key' => 'smtp_username',
                'value' => '',
                'type' => 'text',
                'description' => 'Usuario SMTP',
                'group' => 'email'
            ],
            [
                'key' => 'smtp_encryption',
                'value' => 'tls',
                'type' => 'select',
                'description' => 'Encriptación SMTP',
                'group' => 'email',
                'options' => ['tls', 'ssl', 'none']
            ],

            // Notificaciones
            [
                'key' => 'notify_new_registrations',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Notificar nuevas inscripciones',
                'group' => 'notifications'
            ],
            [
                'key' => 'notify_payment_received',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Notificar pagos recibidos',
                'group' => 'notifications'
            ],

            // Integración Flow
            [
                'key' => 'flow_api_key',
                'value' => '',
                'type' => 'password',
                'description' => 'API Key de Flow',
                'group' => 'payments'
            ],
            [
                'key' => 'flow_secret_key',
                'value' => '',
                'type' => 'password',
                'description' => 'Secret Key de Flow',
                'group' => 'payments'
            ],
            [
                'key' => 'flow_sandbox',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Usar Flow en modo sandbox',
                'group' => 'payments'
            ]
        ];

        foreach ($defaultSettings as $setting) {
            Setting::create($setting);
        }
    }
}
