<?php

namespace App\Events;

use App\Models\MentionNotification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MentionCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $mention;

    public function __construct(MentionNotification $mention)
    {
        $this->mention = $mention->load('message.user', 'message.group');
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->mention->user_id);
    }
}
