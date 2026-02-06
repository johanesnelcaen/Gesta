<div class="p-6 bg-white rounded shadow">
   <h2 class="text-xl font-bold mb-4">
    Mes groupes 
    <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-sm">
        {{ $groups->count() }}
    </span>
</h2>

    @forelse($groups as $group)
        <div class="border rounded p-4 mb-3 flex justify-between items-center">
            <div>
                @if($editingGroupId === $group->id)
                    <input
                        type="text"
                        wire:model.defer="editingGroupName"
                        class="border rounded p-1"
                    >
                    @error('editingGroupName') <span class="text-red-600">{{ $message }}</span> @enderror
                @else
                    <h3 class="text-lg font-semibold">{{ $group->name }}</h3>
                @endif

                <p>Créé par : {{ $group->owner->name }}</p>

              <div class="mt-2">
    <a href="{{ route('groups.show', $group->id) }}" class="text-blue-600 hover:underline">Gérer</a>
    |
    <a href="{{ route('groups.tasks', $group->id) }}" class="text-green-600 hover:underline"> tâches</a>
    |
    <a href="{{ route('groups.report', $group->id) }}" class="text-purple-600 hover:underline">Rapport</a>
    |
    <a href="{{ route('groups.chat', $group->id) }}" class="text-pink-600 hover:underline">Chat</a>
</div>


            </div>

            <div class="flex space-x-3">
                @if($editingGroupId === $group->id)
                    <button
                        wire:click="saveEditing"
                        class="text-green-600 hover:text-green-800"
                        title="Enregistrer"
                    >💾</button>
                    <button
                        wire:click="cancelEditing"
                        class="text-gray-600 hover:text-gray-800"
                        title="Annuler"
                    >❌</button>
                @else
                    <button
                        wire:click="startEditing({{ $group->id }}, '{{ addslashes($group->name) }}')"
                        class="text-yellow-500 hover:text-yellow-700"
                        title="Modifier"
                    >✏️</button>
                    <button
                        wire:click="deleteGroup({{ $group->id }})"
                        class="text-red-500 hover:text-red-700"
                        title="Supprimer"
                        onclick="return confirm('Voulez-vous vraiment supprimer ce groupe ?')"
                    >🗑</button>
                @endif
            </div>
        </div>
    @empty
        <p>Vous n'avez rejoint aucun groupe.</p>
    @endforelse

    @if (session()->has('message'))
        <div class="mt-3 p-2 bg-green-100 text-green-800 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-3 p-2 bg-red-100 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif
</div>
