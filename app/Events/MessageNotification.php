<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Routing;

class MessageNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $routing;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Routing $routing)
    {
        //
        $this->routing = $routing;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notification.' . $this->routing->to_office_id);
    }
    public function broadcastWith()
    {
        return [
            'document_id' => $this->routing->document->document_id,
            'document_type' => $this->routing->document->document_type,
            'from_office' => $this->routing->fromOffice->name,
            'to_office' => $this->routing->toOffice->name,
            'date_forwarded' => $this->routing->date_forwarded,
        ];
    }
}
