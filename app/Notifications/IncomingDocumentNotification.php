<?php

namespace App\Notifications;

use App\Models\Routing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class IncomingDocumentNotification extends Notification
{
    use Queueable;

    public $routing;

    public function __construct(Routing $routing)
    {
        $this->routing = $routing;
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'routing_id' => $this->routing->id,
            'document_id' => $this->routing->document->document_id,
            'message' => 'New incoming document: ' . $this->routing->document->document_type
        ]);
    }
}
