<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\Demanda;
use App\Models\LinkPrestador;

class NewDemandNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $demanda;
    public $link;
    public $prestadorNome;

    /**
     * Create a new notification instance.
     * $link pode ser LinkPrestador ou LinkDemandaPublico
     */
    public function __construct(Demanda $demanda, $link, $prestadorNome = null)
    {
        $this->demanda = $demanda;
        $this->link = $link;
        $this->prestadorNome = $prestadorNome;
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
        $url = $this->getUrl();
        $nome = $this->prestadorNome ?? ($notifiable->nome_razao_social ?? 'Prestador');

        return (new MailMessage)
                    ->subject('Nova Demanda de Serviço: ' . $this->demanda->titulo)
                    ->greeting('Olá, ' . $nome . '!')
                    ->line('Uma nova demanda de serviço compatível com seu perfil foi aberta no sistema CondoCompras.')
                    ->line('**Serviço:** ' . $this->demanda->titulo)
                    ->line('**Condomínio:** ' . ($this->demanda->condominio->nome ?? 'N/A'))
                    ->action('Visualizar Detalhes e Enviar Orçamento', $url)
                    ->line('Aguardamos sua proposta!');
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $url = $this->getUrl();
        $nomePrestador = $this->prestadorNome ?? ($notifiable->nome_razao_social ?? 'Prestador');
        $gerente = $this->demanda->usuario;
        $contatoGerente = $gerente ? "\n\nResponsável: *{$gerente->name}*\n📞 Contato: {$gerente->telefone}" : "";
        
        return [
            'message' => "📋 *Nova Demanda:* {$this->demanda->titulo}\n\nOlá, {$nomePrestador}! Temos uma nova oportunidade de serviço para você no CondoCompras.{$contatoGerente}\n\n🔗 Detalhes e Orçamento: {$url}\n\n⚠️ _Este é um número automático, favor não responder por aqui._",
            'phone' => $this->link->whatsapp ?? $notifiable->celular ?? $notifiable->telefone
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
            'demanda_id' => $this->demanda->id,
            'titulo' => $this->demanda->titulo,
            'link_token' => $this->link->token,
            'type' => 'new_demand'
        ];
    }

    /**
     * Resolve a URL correta baseada no tipo de link
     */
    protected function getUrl(): string
    {
        if ($this->link instanceof \App\Models\LinkPrestador) {
            return route('prestador.link.show', $this->link->token);
        }
        
        return route('publico.demanda.show', $this->link->token);
    }
}
