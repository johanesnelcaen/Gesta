<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\GroupMessage;
use App\Models\MentionNotification;
use App\Events\GroupMessageSent;
use App\Events\MentionCreated;

class GroupChat extends Component
{
    public $group;
    public $message;

    public function mount(Group $group)
    {
        $this->group = $group;
    }

    public function send()
    {
        $this->validate([
            'message' => 'required|string|max:1000'
        ]);

        // Créer le message
        $msg = GroupMessage::create([
            'group_id' => $this->group->id,
            'user_id' => Auth::id(),
            'message' => $this->message,
        ]);

        // ✅ Identifier les @mentions
        preg_match_all('/@(\w+)/', $this->message, $matches);
        $mentionedUsernames = $matches[1] ?? [];

        foreach ($mentionedUsernames as $username) {
            $user = \App\Models\User::where('name', $username)->first();
            if ($user) {
                $mention = MentionNotification::create([
                    'user_id' => $user->id,
                    'message_id' => $msg->id,
                ]);

                // 🔔 Diffuse l'événement pour l'utilisateur mentionné
                broadcast(new MentionCreated($mention))->toOthers();
            }
        }

        // 🔔 Diffuse l'événement de message pour le chat
        broadcast(new GroupMessageSent($msg))->toOthers();

        $this->message = '';
    }

    public $editingMessageId = null;
public $editingMessageText = '';

public function startEditing($messageId)
{
    $msg = GroupMessage::findOrFail($messageId);
    if ($msg->user_id !== Auth::id()) return; // sécurité

    $this->editingMessageId = $msg->id;
    $this->editingMessageText = $msg->message;
}

public function updateMessage()
{
    $msg = GroupMessage::findOrFail($this->editingMessageId);
    if ($msg->user_id !== Auth::id()) return;

    $msg->update([
        'message' => $this->editingMessageText
    ]);

    $this->editingMessageId = null;
    $this->editingMessageText = '';
}

public function deleteMessage($messageId)
{
    $msg = GroupMessage::findOrFail($messageId);
    if ($msg->user_id !== Auth::id()) return;

    $msg->delete();
}


    public function render()
    {
        return view('livewire.group-chat', [
            'messages' => $this->group->messages()
                            ->with('user')
                            ->latest()
                            ->take(50)
                            ->get()
                            ->reverse()
        ]);
    }
}
