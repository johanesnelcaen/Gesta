<?php

namespace App\Livewire\Groups;

use Livewire\Component;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ManageMembers extends Component
{
    public $groupId;
    public $email;
    public $members;

    protected $listeners = ['removeMemberConfirmed' => 'removeMember'];

    public function mount($groupId)
    {
        $this->groupId = $groupId;
        $this->loadMembers();
    }

    private function loadMembers(): void
    {
        $group = Group::findOrFail($this->groupId);
        $this->members = $group->users;
    }

    public function addMember(): void
    {
        $group = Group::findOrFail($this->groupId);

        if (Auth::id() !== $group->owner_id) {
            abort(403, 'Accès refusé');
        }

        $this->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $this->email)->first();

        if (!$group->users->contains($user->id)) {
            $group->users()->attach($user->id);
            $this->loadMembers();

            $this->dispatch('swal:success', [
                'message' => 'Membre ajouté avec succès.'
            ]);
        } else {
            $this->dispatch('swal:error', [
                'message' => 'Utilisateur déjà membre du groupe.'
            ]);
        }

        $this->reset('email');
    }

    public function removeMember($userId): void
    {
        $group = Group::findOrFail($this->groupId);

        if (Auth::id() !== $group->owner_id && Auth::id() !== (int) $userId) {
            abort(403, 'Accès refusé');
        }

        $group->users()->detach($userId);
        $this->loadMembers();

        $this->dispatch('swal:success', [
            'message' => (Auth::id() === (int) $userId)
                ? 'Vous avez quitté le groupe.'
                : 'Membre retiré avec succès.'
        ]);
    }

    public function render()
    {
        $group = Group::findOrFail($this->groupId);

        return view('livewire.groups.manage-members', [
            'group' => $group,
            'members' => $this->members,
        ]);
    }
}
