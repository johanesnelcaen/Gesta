<?php

namespace App\Livewire\Groups;

use Livewire\Component;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class CreateGroup extends Component
{
    public $name;

   public function create()
{
    $this->validate([
        'name' => 'required|string|max:255',
    ]);

    $group = Group::create([
        'name' => $this->name,
        'owner_id' => Auth::id(),
    ]);

    // Ajouter l'admin comme membre du groupe (une seule fois)
    $group->users()->attach(Auth::id());

    session()->flash('success', 'Groupe créé avec succès !');
    $this->reset('name');
}


    public function render()
    {
        return view('livewire.groups.create-group');
    }
}
