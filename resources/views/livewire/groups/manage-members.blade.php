<div class="p-4 bg-white rounded shadow mt-6">
    <div class="flex flex-col md:flex-row gap-4">

        <div class="md:basis-7/12 flex-shrink-0">
            
            {{-- Formulaire ajout membre --}}
            @if(auth()->id() === $group->owner_id)
                <form wire:submit.prevent="addMember" class="mb-4 flex gap-2">
                    <input type="email" wire:model.defer="email" placeholder="Email du membre" class="border p-2 rounded flex-1">
                    @error('email') <span class="text-red-500">{{ $message }}</span> @enderror
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Ajouter</button>
                </form>
            @endif

            {{-- Liste des membres --}}
            <h3 class="font-semibold mb-2">
                Membres du groupe 
                <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-sm">
                    {{ $group->users->count() }}
                </span>
            </h3>

            <ul class="list-disc pl-5">
                @foreach($group->users as $user)
                    <li class="flex justify-between items-center">
                        <span>{{ $user->name }} ({{ $user->email }})</span>

                        {{-- Boutons avec confirmation SweetAlert --}}
                        @if(auth()->id() === $group->owner_id && $user->id !== $group->owner_id)
                            <button onclick="confirmRemoveMember({{ $user->id }})"
                                    class="ml-2 text-red-500 hover:underline">
                                Retirer
                            </button>
                        @elseif(auth()->id() === $user->id)
                            <button onclick="confirmRemoveMember({{ $user->id }})"
                                    class="ml-2 text-orange-500 hover:underline">
                                Quitter
                            </button>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Lottie 30% --}}
        <div class="md:basis-5/12 flex justify-center items-center flex-shrink-0">
            <div id="lottie-container" style="width:100%; max-width:300px;"></div>
        </div>
    </div>

    <script>
        const animation = lottie.loadAnimation({
            container: document.getElementById('lottie-container'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '{{ asset("animations/team work.json") }}'
        });

        // Confirmation SweetAlert avant suppression
        function confirmRemoveMember(userId) {
            Swal.fire({
                title: "Êtes-vous sûr ?",
                text: "Cette action ne peut pas être annulée.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e3342f",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Oui, continuer",
                cancelButtonText: "Annuler"
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('removeMemberConfirmed', userId);
                }
            });
        }

        // Notifications succès / erreur depuis Livewire
        Livewire.on('swal:success', (data) => {
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
        });

        Livewire.on('swal:error', (data) => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
</div>
