<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\Orcamento;

class BudgetStatusNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $orcamento;
    public $status;
    public $url;

    /**
     * Create a new notification instance.
     */
    public function __construct(Orcamento $orcamento, string $status, string $url)
    {
        $this->orcamento = $orcamento;
        $this->status = $status; // aprovado ou rejeitado
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
        $subject = $this->status === 'aprovado' 
            ? '✅ Orçamento Aprovado: ' . $this->orcamento->demanda->titulo
            : '❌ Atualização sobre Orçamento: ' . $this->orcamento->demanda->titulo;

        $message = (new MailMessage)
                    ->subject($subject)
                    ->greeting('Olá, ' . ($notifiable->nome_razao_social ?? 'Prestador') . '!');

        if ($this->status === 'aprovado') {
            $message->line('Ótima notícia! Seu orçamento para a demanda **' . $this->orcamento->demanda->titulo . '** foi **APROVADO**.')
                    ->line('Acesse os detalhes abaixo para agendar e concluir o serviço.')
                    ->action('Ver Detalhes do Serviço', $this->url)
                    ->line('Parabéns!');
        } else {
            $message->line('Informamos que seu orçamento para a demanda **' . $this->orcamento->demanda->titulo . '** não foi selecionado desta vez.')
                    ->line('Agradecemos o envio da sua proposta e esperamos contar com você em futuras oportunidades.')
                    ->action('Ver Demanda', $this->url);
        }

        return $message;
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $gerente = $this->orcamento->demanda->usuario;
        $contatoGerente = $gerente ? "\n\nResponsável: *{$gerente->name}*\n📞 Contato: {$gerente->telefone}" : "";

        if ($this->status === 'aprovado') {
            $msg = "✅ *Ótima notícia!* Seu orçamento para a demanda *{$this->orcamento->demanda->titulo}* foi *APROVADO*.{$contatoGerente}\n\n🔗 Acesse os detalhes aqui: {$this->url}\n\n⚠️ _Número automático, não responda aqui._";
        } else {
            $msg = "📢 *Atualização de Status:* Informamos que seu orçamento para a demanda *{$this->orcamento->demanda->titulo}* não foi selecionado desta vez.{$contatoGerente}\n\n🔗 Ver detalhes: {$this->url}\n\n⚠️ _Número automático, não responda aqui._";
        }

        return [
            'message' => $msg,
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
            'status' => $this->status,
            'type' => 'budget_status'
        ];
    }
}
