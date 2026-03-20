<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // WhatsApp Settings
            [
                'key' => 'whatsapp_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'whatsapp',
                'description' => 'Habilita ou desabilita o envio automático via WhatsApp API',
            ],
            [
                'key' => 'whatsapp_api_url',
                'value' => '',
                'type' => 'string',
                'group' => 'whatsapp',
                'description' => 'URL Base da API de WhatsApp (ex: Evolution API)',
            ],
            [
                'key' => 'whatsapp_api_key',
                'value' => '',
                'type' => 'string',
                'group' => 'whatsapp',
                'description' => 'Chave de API (ApyKey/Token) do serviço de WhatsApp',
            ],
            [
                'key' => 'whatsapp_instance_id',
                'value' => '',
                'type' => 'string',
                'group' => 'whatsapp',
                'description' => 'Identificador da instância vinculada (ex: Numero do Celular ou Nome da Instância)',
            ],

            // Email Settings
            [
                'key' => 'email_notifications_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'description' => 'Habilita ou desabilita o envio de notificações por E-mail',
            ],

            // General Notification Controls
            [
                'key' => 'notify_new_demand',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'triggers',
                'description' => 'Notificar prestadores sobre novas demandas compatíveis',
            ],
            [
                'key' => 'notify_negotiation',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'triggers',
                'description' => 'Notificar quando uma nova negociação for solicitada',
            ],
            [
                'key' => 'notify_budget_status',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'triggers',
                'description' => 'Notificar sobre aprovação ou rejeição de orçamentos',
            ],
        ];

        foreach ($settings as $setting) {
            \App\Models\NotificationSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
