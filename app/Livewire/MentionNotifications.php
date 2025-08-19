<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\MentionNotification;

class MentionNotifications extends Component
{
    public $notifications = [];
    public $count = 0;

    protected $listeners = [];

    public function mount()
    {
        $this->listeners['echo:private-user.' . Auth::id() . ',MentionCreated'] = 'newMention';
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Auth::user()
            ->mentionNotifications()
            ->where('read', false)
            ->with('message.group', 'message.user')
            ->get();

        $this->count = $this->notifications->count();
    }

    public function newMention($data)
    {
        $this->loadNotifications();
    }

    public function markAsRead($id)
    {
        MentionNotification::find($id)?->update(['read' => true]);
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.mention-notifications');
    }
}
