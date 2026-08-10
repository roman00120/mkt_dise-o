<?php

namespace App\Notifications;

use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreativeRequestCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(public CreativeRequest $creativeRequest, public ?User $actor = null) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[Solicitud Completada] '.$this->creativeRequest->folio.' | '.$this->creativeRequest->service?->label().' | '.$this->creativeRequest->title)
            ->view('emails.creative.request-notification', [
                'emailSubject' => 'Solicitud completada · '.$this->creativeRequest->folio,
                'badge' => 'Solicitud completada',
                'recipientName' => $notifiable->name,
                'intro' => '¡Tu solicitud ha sido finalizada con éxito! El entregable final ya se encuentra disponible para consultar y descargar.',
                'requestModel' => $this->creativeRequest,
                'status' => 'Completada',
                'actionUrl' => route('app.requests.show', $this->creativeRequest),
                'actionLabel' => 'Ver entregables',
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event_type' => 'request_completed',
            'title' => 'Solicitud completada',
            'message' => $this->creativeRequest->folio.' · '.$this->creativeRequest->title,
            'url' => route('app.requests.show', $this->creativeRequest),
            'entity_type' => 'creative_request',
            'entity_id' => $this->creativeRequest->id,
            'actor_id' => $this->actor?->id,
            'icon' => 'check-circle',
        ];
    }
}
