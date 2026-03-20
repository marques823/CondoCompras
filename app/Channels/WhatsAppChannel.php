<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        // Tenta pegar a administradora_id do contexto (ex: da Demanda)
        $administradoraId = null;
        if (isset($notification->demanda)) {
            $administradoraId = $notification->demanda->administradora_id;
        } elseif (isset($notification->orcamento->demanda)) {
            $administradoraId = $notification->orcamento->demanda->administradora_id;
        } elseif (isset($notifiable->administradora_id)) {
            $administradoraId = $notifiable->administradora_id;
        }

        // Função auxiliar para buscar com fallback
        $getSetting = function($key) use ($administradoraId) {
            // Primeiro tenta a específica da empresa
            if ($administradoraId) {
                $specific = NotificationSetting::where('key', $key)
                    ->where('administradora_id', $administradoraId)
                    ->first();
                if ($specific && !empty($specific->value)) return $specific->value;
            }
            // Fallback para global
            return NotificationSetting::where('key', $key)
                ->whereNull('administradora_id')
                ->first()?->value;
        };

        if ($getSetting('whatsapp_enabled') !== '1') {
            return;
        }

        $apiUrl = $getSetting('whatsapp_api_url');
        $apiKey = $getSetting('whatsapp_api_key');
        $instanceId = $getSetting('whatsapp_instance_id');

        if (!$apiUrl || !$apiKey || !$instanceId) {
            Log::warning('WhatsApp API credentials not fully configured for Administradora: ' . ($administradoraId ?? 'Global'));
            return;
        }

        $data = $notification->toWhatsApp($notifiable);
        $phone = $this->formatPhone($data['phone'] ?? $notifiable->celular ?? $notifiable->telefone);
        

        if (!$phone) {
            Log::warning('No valid phone number found for WhatsApp notification.', ['notifiable_id' => $notifiable->id]);
            return;
        }

        try {
            $apiUrl = trim($apiUrl);
            $url = rtrim($apiUrl, '/') . "/message/sendText/{$instanceId}";
            
            $response = Http::withHeaders([
                'apikey' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'number' => $phone,
                'text' => $data['message'],
                'delay' => 1200,
                'linkPreview' => true,
            ]);

            if ($response->failed()) {
                Log::error('WhatsApp notification failed to send.', [
                    'url' => $url,
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'phone' => $phone
                ]);
            } else {
                Log::info("WhatsApp Message Sent successfully to $phone. Status: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp notification: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => substr($e->getTraceAsString(), 0, 500)
            ]);
        }
    }

    /**
     * Formata o número para o padrão internacional sem caracteres especiais
     */
    protected function formatPhone($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);
        
        if (empty($phone)) return null;

        // Se não tiver DDI, assume Brasil (55)
        if (strlen($phone) <= 11) {
            $phone = '55' . $phone;
        }

        return $phone;
    }
}
