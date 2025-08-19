<div class="p-4 bg-white rounded shadow mt-6">

    {{-- Bloc formulaire + Lottie côte à côte --}}
    <div class="flex gap-4 flex-col md:flex-row">

        {{-- Formulaire création de tâche (70%) --}}
        @if(auth()->id() === $group->owner_id)
            <div class="bg-gray-50 rounded shadow p-4 md:basis-7/12 flex-shrink-0">
                <form wire:submit.prevent="createTask" class="space-y-3">
                    {{-- Titre + assignation --}}
                    <div class="flex gap-3">
                        <input type="text" wire:model="title" placeholder="Titre de la tâche"
                               class="border px-2 py-1 rounded w-3/4">

                        <select wire:model="assigned_to" class="border px-2 py-1 rounded w-1/4">
                            <option value="">-- Attribuer à --</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date début + fin --}}
                    <div class="flex gap-3">
                        <div class="w-1/2">
                            <label class="block text-sm font-semibold">Date de début</label>
                            <input type="date" wire:model="start" class="border px-2 py-1 rounded w-full">
                        </div>

                        <div class="w-1/2">
                            <label class="block text-sm font-semibold">Date de fin</label>
                            <input type="date" wire:model="end" class="border px-2 py-1 rounded w-full">
                        </div>
                    </div>

                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                        Créer
                    </button>

                    @if ($successMessage)
                        <div class="mt-2 text-sm text-green-600">{{ $successMessage }}</div>
                    @endif
                </form>
            </div>
        @endif

        {{-- Lottie (30%) --}}
        <div class="md:basis-5/12 flex justify-center items-center flex-shrink-0">
            <div id="lottie-container" style="width:100%; max-width:300px;"></div>
        </div>

    </div>

    {{-- Liste des tâches --}}
<ul class="divide-y mt-6">
    @forelse ($tasks as $task)
        <li class="flex justify-between items-center py-2">
            <div>
                <strong>{{ $task->title }}</strong> <br>
                <span class="text-sm text-blue-600">Assignée à :</span> {{ $task->assignedUser?->name ?? 'Non assignée' }} <br>
                <span class="text-sm text-blue-600">Statut :</span> {{ $task->is_completed ? 'Terminée' : 'En cours' }} <br>
                <span class="text-sm text-gray-600">Du {{ $task->start }} au {{ $task->end }}</span>
            </div>

            @if(auth()->id() === $group->owner_id)
                <div class="flex gap-2">
                    <button wire:click="editTask({{ $task->id }})" 
                        class="bg-blue-600 text-white px-2 py-1 rounded">
                        Modifier
                    </button>
                    <button wire:click="deleteTask({{ $task->id }})" 
                        class="bg-red-600 text-white px-2 py-1 rounded"
                        onclick="return confirm('Supprimer cette tâche ?')">
                        Supprimer
                    </button>
                </div>
            @endif
        </li>
    @empty
        <li class="py-2">Aucune tâche encore.</li>
    @endforelse
</ul>

{{-- Le formulaire utilisera automatiquement les données si $editingTaskId est défini --}}
<button class="mt-2 text-sm text-gray-500">
    @if($editingTaskId) Mode modification activé @endif
</button>


    {{-- Animation Lottie --}}
    <script>
        const animation = lottie.loadAnimation({
            container: document.getElementById('lottie-container'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '{{ asset("animations/Add Document.json") }}'
        });
    </script>
</div>
