<?php

namespace App\Events;

use App\Models\GroupMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class GroupMessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(GroupMessage $message) // <--- ici
    {
        $this->message = $message->load('user');
    }

    public function broadcastOn()
    {
        return new Channel('group.' . $this->message->group_id);
    }
}
