<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Orcamento;

class BudgetReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $orcamento;

    /**
     * Create a new notification instance.
     */
    public function __construct(Orcamento $orcamento)
    {
        $this->orcamento = $orcamento;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('demandas.show', $this->orcamento->demanda_id);

        return (new MailMessage)
                    ->subject('Novo Orçamento Recebido: ' . $this->orcamento->demanda->titulo)
                    ->greeting('Olá, ' . $notifiable->name . '!')
                    ->line('Um novo orçamento foi enviado pelo prestador **' . $this->orcamento->prestador->nome_razao_social . '**.')
                    ->line('**Demanda:** ' . $this->orcamento->demanda->titulo)
                    ->line('**Valor:** R$ ' . number_format($this->orcamento->valor, 2, ',', '.'))
                    ->action('Visualizar Orçamento', $url)
                    ->line('Acesse o painel para analisar a proposta.');
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
            'demanda_id' => $this->orcamento->demanda_id,
            'prestador_nome' => $this->orcamento->prestador->nome_razao_social,
            'valor' => $this->orcamento->valor,
            'type' => 'budget_received'
        ];
    }
}
