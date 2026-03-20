<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\Orcamento;
use App\Models\Negociacao;

class NegotiationRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $orcamento;
    public $negociacao;
    public $url;

    /**
     * Create a new notification instance.
     */
    public function __construct(Orcamento $orcamento, Negociacao $negociacao, string $url)
    {
        $this->orcamento = $orcamento;
        $this->negociacao = $negociacao;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', WhatsAppChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $tipoLabel = $this->negociacao->tipo == 'desconto' ? 'Desconto' : ($this->negociacao->tipo == 'parcelamento' ? 'Parcelamento' : 'Contraproposta');

        return (new MailMessage)
                    ->subject('Solicitação de Negociação: ' . $this->orcamento->demanda->titulo)
                    ->greeting('Olá, ' . ($notifiable->nome_razao_social ?? 'Prestador') . '!')
                    ->line('A administração enviou uma proposta de ' . strtolower($tipoLabel) . ' para o seu orçamento.')
                    ->line('**Demanda:** ' . $this->orcamento->demanda->titulo)
                    ->line('**Mensagem da Adm:** ' . $this->negociacao->mensagem_solicitacao)
                    ->action('Ver Proposta e Responder', $this->url)
                    ->line('Por favor, visualize os detalhes e responda através do link acima.');
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $tipoLabel = $this->negociacao->tipo == 'desconto' ? 'DESCONTO' : ($this->negociacao->tipo == 'parcelamento' ? 'PARCELAMENTO' : 'CONTRAPROPOSTA');
        $gerente = $this->orcamento->demanda->usuario;
        $contatoGerente = $gerente ? "\n\nResponsável: *{$gerente->name}*\n📞 Contato: {$gerente->telefone}" : "";

        return [
            'message' => "🤝 *Solicitação de {$tipoLabel}*\n\nOlá, " . ($notifiable->nome_razao_social ?? 'Prestador') . "! A administração enviou uma proposta para o seu orçamento na demanda: *{$this->orcamento->demanda->titulo}*.\n\n💬 Mensagem: _{$this->negociacao->mensagem_solicitacao}_{$contatoGerente}\n\n🔗 Responda aqui: {$this->url}\n\n⚠️ _Número automático, não responda aqui._",
            'phone' => $notifiable->celular ?? $notifiable->telefone
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'orcamento_id' => $this->orcamento->id,
            'negociacao_id' => $this->negociacao->id,
            'tipo' => $this->negociacao->tipo,
            'type' => 'negotiation_requested'
        ];
    }
}
