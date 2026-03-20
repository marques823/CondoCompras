<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationSetting;

class NotificationSettingController extends Controller
{
    /**
     * Exibe as configurações de notificação
     */
    public function index()
    {
        $administradoraId = auth()->user()->administradora_id;

        // Se for super-admin (sem administradora_id), gerencia apenas os globais
        if (auth()->user()->isAdmin() && !$administradoraId) {
            $settings = NotificationSetting::whereNull('administradora_id')->get()->groupBy('group');
            return view('admin.notification-settings.index', compact('settings'));
        }

        // Para Administradoras: Busca os padrões globais
        $defaultSettings = NotificationSetting::whereNull('administradora_id')->get();
        // Busca as configurações específicas da empresa
        $companySettings = NotificationSetting::where('administradora_id', $administradoraId)->get()->keyBy('key');

        // Mescla: Usa o valor da empresa se existir, senão usa o padrão mas mantém o objeto para renderizar
        $settings = $defaultSettings->map(function ($setting) use ($companySettings) {
            if ($companySettings->has($setting->key)) {
                $setting->value = $companySettings->get($setting->key)->value;
            }
            return $setting;
        })->groupBy('group');

        return view('admin.notification-settings.index', compact('settings'));
    }

    /**
     * Atualiza as configurações de notificação
     */
    public function update(Request $request)
    {
        $administradoraId = auth()->user()->administradora_id;
        
        // Se for admin mas não tiver empresa vinculada, edita o global
        $isGlobalAdmin = auth()->user()->isAdmin() && !$administradoraId;

        $inputData = $request->except('_token');

        // Lista de todas as chaves que vieram no request ou que são booleans conhecidos
        $allKeys = NotificationSetting::whereNull('administradora_id')->pluck('key');

        foreach ($allKeys as $key) {
            $template = NotificationSetting::where('key', $key)->whereNull('administradora_id')->first();
            if (!$template) continue;

            $value = $request->input($key);
            
            // Trata checkbox (boolean) que não vem no request se desmarcado
            if ($template->type === 'boolean' && !$request->has($key)) {
                $value = '0';
            }

            // Se for admin global, atualiza o global. Se for empresa, cria/atualiza o dela.
            if ($isGlobalAdmin) {
                $template->update(['value' => $value]);
            } else {
                NotificationSetting::updateOrCreate(
                    ['key' => $key, 'administradora_id' => $administradoraId],
                    [
                        'value' => $value,
                        'type' => $template->type,
                        'group' => $template->group,
                        'description' => $template->description
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Configurações de notificação atualizadas com sucesso!');
    }
}
