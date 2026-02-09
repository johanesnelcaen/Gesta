<?php

namespace App\Livewire\Groups;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]

class MyGroups extends Component
{
    public $groups;

    public $editingGroupId = null;
    public $editingGroupName = '';

    public function mount()
    {
        $this->loadGroups();
    }

    public function loadGroups()
    {
        $this->groups = Auth::user()->groups()->with('owner')->get();
    }

    public function startEditing($groupId, $currentName)
    {
        $this->editingGroupId = $groupId;
        $this->editingGroupName = $currentName;
    }

    public function cancelEditing()
    {
        $this->editingGroupId = null;
        $this->editingGroupName = '';
    }

    public function saveEditing()
    {
        $this->validate([
            'editingGroupName' => 'required|string|max:255',
        ]);

        $group = Group::findOrFail($this->editingGroupId);

        if (Auth::id() !== $group->owner_id) {
            session()->flash('error', 'Vous ne pouvez pas modifier ce groupe.');
            $this->cancelEditing();
            return;
        }

        $group->name = $this->editingGroupName;
        $group->save();

        session()->flash('message', 'Groupe modifié avec succès.');

        $this->cancelEditing();
        $this->loadGroups();
    }

    public function deleteGroup($groupId)
    {
        $group = Group::findOrFail($groupId);

        if (Auth::id() !== $group->owner_id) {
            session()->flash('error', 'Vous ne pouvez pas supprimer ce groupe.');
            return;
        }

        $group->delete();
        session()->flash('message', 'Groupe supprimé avec succès.');

        $this->loadGroups();
    }

    public function render()
{
    return view('livewire.groups.my-groups')
        ->layout('layouts.app');
}

}
