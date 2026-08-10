<?php

namespace App\Notifications;

use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreativeRequestAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(public CreativeRequest $creativeRequest, public User $actor) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[Solicitud Asignada] '.$this->creativeRequest->folio.' | '.$this->creativeRequest->service?->label().' | '.$this->creativeRequest->title)
            ->view('emails.creative.request-notification', [
                'emailSubject' => 'Solicitud asignada · '.$this->creativeRequest->folio,
                'badge' => 'Solicitud asignada',
                'recipientName' => $notifiable->name,
                'intro' => 'Hugo ha validado y asignado esta solicitud. Ya puedes comenzar a trabajar en ella.',
                'requestModel' => $this->creativeRequest,
                'status' => 'Asignada',
                'actionUrl' => route('creative.requests.show', $this->creativeRequest),
                'actionLabel' => 'Ver solicitud',
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event_type' => 'assignment',
            'title' => 'Solicitud asignada',
            'message' => $this->creativeRequest->folio.' · '.$this->creativeRequest->title,
            'url' => route('creative.requests.show', $this->creativeRequest),
            'entity_type' => 'creative_request',
            'entity_id' => $this->creativeRequest->id,
            'actor_id' => $this->actor->id,
            'icon' => 'clipboard-check',
        ];
    }
}
