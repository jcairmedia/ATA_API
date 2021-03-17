<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserSendMeetingEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $contact;
    public $meeting;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($meeting, $contact)
    {
        $this->meeting = $meeting;
        $this->contact = $contact;
        \Log::error('UserSendMeetingEvent: '.print_r($this->contact, 1));
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
