<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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
            [
                'key' => 'website_url',
                'value' => 'https://bmxchile.cl',
                'type' => 'url',
                'description' => 'URL del sitio web oficial',
                'group' => 'general'
            ],

            // Configuraciones del sistema
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Modo de mantenimiento',
                'group' => 'sistema'
            ],
            [
                'key' => 'timezone',
                'value' => 'America/Santiago',
                'type' => 'select',
                'description' => 'Zona horaria del sistema',
                'group' => 'sistema',
                'options' => ['America/Santiago', 'UTC', 'America/New_York', 'Europe/Madrid']
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'select',
                'description' => 'Formato de fecha',
                'group' => 'sistema',
                'options' => ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y']
            ],
            [
                'key' => 'max_upload_size',
                'value' => '10',
                'type' => 'number',
                'description' => 'Tamaño máximo de archivo (MB)',
                'group' => 'sistema'
            ],

            // Configuraciones de competencia
            [
                'key' => 'default_registration_fee',
                'value' => '15000',
                'type' => 'number',
                'description' => 'Cuota de inscripción por defecto (CLP)',
                'group' => 'competencia'
            ],
            [
                'key' => 'max_participants_per_category',
                'value' => '50',
                'type' => 'number',
                'description' => 'Máximo de participantes por categoría',
                'group' => 'competencia'
            ],
            [
                'key' => 'registration_deadline_hours',
                'value' => '24',
                'type' => 'number',
                'description' => 'Horas límite para inscripción antes del evento',
                'group' => 'competencia'
            ],
            [
                'key' => 'allow_late_registration',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Permitir inscripción tardía',
                'group' => 'competencia'
            ],

            // Configuraciones de email
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@bmxchile.cl',
                'type' => 'email',
                'description' => 'Email remitente',
                'group' => 'email'
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'Sistema BMX',
                'type' => 'text',
                'description' => 'Nombre del remitente',
                'group' => 'email'
            ],
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
                'type' => 'number',
                'description' => 'Puerto SMTP',
                'group' => 'email'
            ],

            // Configuraciones de notificaciones
            [
                'key' => 'send_registration_emails',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enviar emails de confirmación de registro',
                'group' => 'notificaciones'
            ],
            [
                'key' => 'send_payment_notifications',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enviar notificaciones de pago',
                'group' => 'notificaciones'
            ],

            // Configuraciones de pagos (Flow)
            [
                'key' => 'flow_api_key',
                'value' => '',
                'type' => 'password',
                'description' => 'API Key de Flow',
                'group' => 'pagos'
            ],
            [
                'key' => 'flow_secret_key',
                'value' => '',
                'type' => 'password',
                'description' => 'Secret Key de Flow',
                'group' => 'pagos'
            ]
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
