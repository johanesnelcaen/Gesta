<?php

namespace App\Livewire\Groups;

use Livewire\Component;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ManageMembers extends Component
{
    /**
     * @var int ID du groupe
     */
    public $groupId;

    /**
     * @var string Email du membre à ajouter
     */
    public $email;

    /**
     * @var string Message de retour
     */
    public $message = '';

    /**
     * @var \Illuminate\Support\Collection|\App\Models\User[] Membres du groupe
     */
    public $members;

    public function mount($groupId)
    {
        $this->groupId = $groupId;
        $this->loadMembers();
    }

    /**
     * Charger les membres du groupe
     */
    private function loadMembers(): void
    {
        $group = Group::findOrFail($this->groupId);
        $this->members = $group->users; // Relation users() doit exister dans Group
    }

    /**
     * Ajouter un membre au groupe
     */
    public function addMember(): void
    {
        $group = Group::findOrFail($this->groupId);

        // Vérification du propriétaire
        if (Auth::id() !== $group->owner_id) {
            abort(403, 'Accès refusé');
        }

        $this->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $this->email)->first();

        if (!$group->users->contains($user->id)) {
            $group->users()->attach($user->id);
            $this->loadMembers(); // rafraîchir la liste
            $this->message = 'Membre ajouté avec succès.';
        } else {
            $this->message = 'Utilisateur déjà membre du groupe.';
        }

        $this->reset('email');
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
