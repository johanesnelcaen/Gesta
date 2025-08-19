<div class="border p-4 rounded bg-white shadow">
    <div class="space-y-2 max-h-72 overflow-y-auto" id="messages">
         
        @foreach($messages as $msg)
            @php
                $messageText = preg_replace('/@(\w+)/', '<span class="text-blue-600 font-bold">@$1</span>', e($msg->message));
            @endphp
            <div 
                class="p-2 rounded {{ $msg->user_id === Auth::id() ? 'bg-blue-100 text-right' : 'bg-gray-100 text-left' }}"
                x-data="{ showModal: false }"
                @dblclick="showModal = true"
                @mousedown="pressTimer = setTimeout(() => showModal = true, 800)"
                @mouseup="clearTimeout(pressTimer)"
                @mouseleave="clearTimeout(pressTimer)"
            >
                <strong>{{ $msg->user->name }}</strong>
                <span class="text-xs text-gray-500">• {{ $msg->created_at->diffForHumans() }}</span>
                <p>{!! $messageText !!}</p>

                <!-- Modal -->
                <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                    <div class="bg-white p-4 rounded shadow-lg">
                        <h3 class="text-lg font-bold mb-4">Actions sur le message</h3>
                        <button class="bg-blue-500 text-white px-4 py-2 rounded mr-2" 
                                wire:click="startEditing({{ $msg->id }})" 
                                @click="showModal = false">
                            Modifier
                        </button>
                        <button class="bg-red-500 text-white px-4 py-2 rounded" 
                                wire:click="deleteMessage({{ $msg->id }})" 
                                @click="showModal = false">
                            Supprimer
                        </button>
                        <button class="mt-2 text-gray-500" @click="showModal = false">Annuler</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Formulaire d'envoi ou édition -->
    <div class="flex items-center space-x-4 mt-3">
    <!-- Lottie -->
    <div id="lottie-container" style="width:80px; height:80px;"></div>

    <!-- Formulaire -->
    <form wire:submit.prevent="{{ $editingMessageId ? 'updateMessage' : 'send' }}" class="flex-1 flex">
        <input type="text" wire:model.defer="{{ $editingMessageId ? 'editingMessageText' : 'message' }}" 
               placeholder="Écrire un message... @nom" class="border p-2 flex-1 rounded">
        <button type="submit" class="bg-blue-500 text-white px-4 rounded ml-2">
            {{ $editingMessageId ? 'Mettre à jour' : 'Envoyer' }}
        </button>
    </form>
</div>

</div>


<script>
    // Écoute les messages du groupe
    Echo.channel('group.{{ $group->id }}')
        .listen('GroupMessageSent', (e) => {
            let container = document.getElementById('chat-container');
            // Ici tu ajoutes le nouveau message dans le chat
            container.innerHTML += `
                <div>
                    <strong>${e.message.user.name}</strong>: ${e.message.message}
                </div>
            `;
        });

    // Écoute les mentions pour l'utilisateur connecté
    Echo.private('user.{{ Auth::id() }}')
        .listen('MentionCreated', (e) => {
            alert('Vous avez été mentionné dans un message !');
            // Tu peux aussi mettre à jour ton composant Livewire des notifications ici
        });
</script>
  {{-- Animation Lottie --}}
    <script>
        const animation = lottie.loadAnimation({
            container: document.getElementById('lottie-container'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '{{ asset("animations/chat.json") }}'
        });
    </script>