<?php

namespace App\Notifications;

use App\Models\CreativeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreativeRequestSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public CreativeRequest $creativeRequest) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[Nueva Solicitud] '.$this->creativeRequest->folio.' | '.$this->creativeRequest->service?->label().' | '.$this->creativeRequest->title)
            ->view('emails.creative.request-notification', [
                'emailSubject' => 'Nueva solicitud · '.$this->creativeRequest->folio,
                'badge' => 'Nueva solicitud',
                'recipientName' => $notifiable->name,
                'intro' => $this->creativeRequest->requester?->name.' ha creado una nueva solicitud que requiere atención de tu equipo.',
                'requestModel' => $this->creativeRequest,
                'status' => 'Pendiente de validación',
                'actionUrl' => route('admin.requests.show', $this->creativeRequest),
                'actionLabel' => 'Ver solicitud',
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event_type' => 'request_submitted',
            'title' => 'Nueva solicitud pendiente de validación',
            'message' => $this->creativeRequest->folio.' · '.$this->creativeRequest->title,
            'url' => route('admin.requests.show', $this->creativeRequest),
            'entity_type' => 'creative_request',
            'entity_id' => $this->creativeRequest->id,
            'actor_id' => $this->creativeRequest->requester_id,
            'icon' => 'inbox',
        ];
    }
}
