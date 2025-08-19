
<div class="max-w-xl mx-auto py-8">
     <div id="lottie-container" style=" width: 50%;
    max-width: 400px;
    margin: auto;"></div>
    
 <!-- Bouton pour afficher/masquer le formulaire -->
    <button wire:click="toggleForm"
            class="mb-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
        {{ $showForm ? 'Fermer' : 'Ajouter une tâche' }}
    </button>

    <!-- Formulaire dans une flashcard -->
    @if ($showForm)
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6 border border-gray-200">
           <form wire:submit.prevent="addTask" class="mb-6 space-y-3">
    <div>
        <input type="text" wire:model="title" placeholder="Nouvelle tâche..."
               class="border rounded p-2 w-full mb-2">
        @error('title') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>

    <div class="flex gap-4 mb-2">
        <div class="w-1/2">
            <label class="text-sm">Début :</label>
            <input type="datetime-local" wire:model="start" class="border rounded w-full px-2 py-1 text-sm" />
        </div>

        <div class="w-1/2">
            <label class="text-sm">Fin (facultatif) :</label>
            <input type="datetime-local" wire:model="end" class="border rounded w-full px-2 py-1 text-sm" />
        </div>
    </div>

   <div class="flex items-center gap-2">
    <input type="checkbox" wire:model="is_project" id="is_project" class="accent-blue-600">
    <label for="is_project" class="text-sm">Ceci est un projet (avec sous-tâches)</label>
</div>

<div class="flex items-center gap-2">
    <input type="checkbox" wire:model="is_urgent" id="is_urgent" class="accent-red-600">
    <label for="is_urgent" class="text-sm text-red-600 font-semibold">Tâche urgente</label>
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Ajouter
    </button>
</form>

        </div>
    @endif
    
   <ul>
    @foreach ($tasks as $task)
        @if (!$task->parent_id)
            <li class="mb-4 p-2 border rounded shadow-sm @if($task->is_completed) bg-green-100 @endif">
                <div class="flex justify-between items-center">
                   <span class="@if($task->is_completed) line-through text-gray-500 @endif">
    {{ $task->title }}
    
    @if ($task->is_urgent)
        <span class="text-red-600 font-bold">*</span>
    @endif

    @if ($task->is_project)
        <span class="text-xs text-blue-500 ml-2">(Projet)</span>
    @endif

     @if ($task->group)
        <span class="text-xs text-green-600 ml-2">({{ $task->group->name }})</span>
    @endif
</span>

                    
                    <div class="space-x-2 flex items-center">
                       
                        <button wire:click="toggleCompleted({{ $task->id }})"
                                class="text-sm px-2 py-1 bg-yellow-400 text-white rounded">
                            {{ $task->is_completed ? 'Annuler' : 'Terminer' }}
                        </button>
                          <!-- Bouton modifier -->
<button wire:click="editTask({{ $task->id }})"
        >✏️</button>

                      <button wire:click="deleteTask({{ $task->id }})"
    class="text-red-600 hover:text-red-800 text-lg" title="Supprimer">
    <i class="fas fa-trash-alt"></i>
</button>


                        @if ($task->is_project)
                           <button wire:click="toggleSubtaskForm({{ $task->id }})"
        class="text-sm px-2 py-1 bg-blue-500 text-white rounded">
    {{ $showSubtaskForm[$task->id] ?? false ? 'Fermer' : '+' }}
</button>


                            {{-- Bouton icône pour déplier/replier --}}
                            <button wire:click="toggleSubtasks({{ $task->id }})"
                                    class="text-sm p-2 rounded-full text-gray-700 hover:text-black">
                                @if (in_array($task->id, $expanded))
                                    <i class="fas fa-chevron-up"></i>
                                @else
                                    <i class="fas fa-chevron-down"></i>
                                @endif
                            </button>
                        @endif
                    </div>
                </div>

               

<!-- Formulaire d'édition -->
@if ($editingTaskId === $task->id)
    <div class="mt-2 space-y-2">
        <input type="text" wire:model.defer="editTaskData.title" class="border p-1 w-full rounded" />
        <input type="datetime-local" wire:model.defer="editTaskData.start" class="border p-1 w-full rounded" />
        <input type="datetime-local" wire:model.defer="editTaskData.end" class="border p-1 w-full rounded" />

        @error('editTaskData.start') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        @error('editTaskData.end') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror

        <button wire:click="updateTask" class="bg-blue-600 text-white px-2 py-1 rounded">Enregistrer</button>
        <button wire:click="$set('editingTaskId', null)" class="text-sm text-gray-500 ml-2">Annuler</button>
    </div>
@endif


               {{-- Sous-tâche formulaire --}}
               
@if (!empty($showSubtaskForm[$task->id]))
    <div class="mt-2 space-y-2">
        <input type="text" wire:model.defer="subtaskTitle.{{ $task->id }}" placeholder="Titre sous-tâche"
               class="border p-1 w-full rounded mb-1" />

        {{-- Champ date début --}}
        <input type="datetime-local" 
               wire:model.defer="subtaskStart.{{ $task->id }}" 
               min="{{ $task->start }}" 
               max="{{ $task->end }}"
               class="border p-1 w-full rounded mb-1"
               placeholder="Début" />

        {{-- Champ date fin --}}
        <input type="datetime-local" 
               wire:model.defer="subtaskEnd.{{ $task->id }}" 
               min="{{ $task->start }}" 
               max="{{ $task->end }}"
               class="border p-1 w-full rounded mb-1"
               placeholder="Fin" />

        {{-- Erreur si dates en dehors de l'intervalle --}}
        @error("subtaskDateRange.$task->id") 
            <p class="text-red-500 text-sm">{{ $message }}</p> 
        @enderror

        <button wire:click="addSubtask({{ $task->id }})"
                class="bg-green-500 text-white px-2 py-1 rounded text-sm">Ajouter</button>
    </div>
@endif


                {{-- Affichage des sous-tâches uniquement si déplié --}}
                @if ($task->subtasks->count() && in_array($task->id, $expanded))
                    <ul class="ml-4 mt-2 list-disc text-sm">
                        @foreach ($task->subtasks as $subtask)
                            <li class="flex justify-between items-center">
                                <span class="@if($subtask->is_completed) line-through text-gray-500 @endif">
                                    {{ $subtask->title }}
                                </span>
                                <div>
                                    

                                    <button wire:click="toggleCompleted({{ $subtask->id }})"
                                            class="text-xs px-2 py-1 bg-yellow-300 text-white rounded">
                                        {{ $subtask->is_completed ? 'Annuler' : 'Terminer' }}
                                    </button>
                                     <!-- Bouton modifier sous-tâche -->
<button wire:click="editSubtask({{ $subtask->id }})"
        class="text-yellow-600 text-sm">✏️ </button>
                                   <button wire:click="deleteTask({{ $task->id }})"
    class="text-red-600 hover:text-red-800 text-lg" title="Supprimer">
    <i class="fas fa-trash-alt"></i>
</button>

                                </div>
                            </li>
                           
<!-- Formulaire d'édition de sous-tâche -->
@if ($editingSubtaskId === $subtask->id)
    <div class="mt-2 space-y-2">
        <input type="text" wire:model.defer="editSubtaskData.title" class="border p-1 w-full rounded" />
        <input type="datetime-local" wire:model.defer="editSubtaskData.start" class="border p-1 w-full rounded" />
        <input type="datetime-local" wire:model.defer="editSubtaskData.end" class="border p-1 w-full rounded" />

        @error('editSubtaskData.end') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror

        <button wire:click="updateSubtask" class="bg-blue-500 text-white px-2 py-1 rounded">Mettre à jour</button>
        <button wire:click="$set('editingSubtaskId', null)" class="text-sm text-gray-500 ml-2">Annuler</button>
    </div>
@endif

                        @endforeach
                    </ul>
                @endif
            </li>
        @endif
    @endforeach
</ul>
    <script>
        // Charger l'animation Lottie depuis un lien JSON public
        const animation = lottie.loadAnimation({
            container: document.getElementById('lottie-container'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '{{ asset("animations/Work in progress.json") }}'
        });
    </script>
</div>
  


