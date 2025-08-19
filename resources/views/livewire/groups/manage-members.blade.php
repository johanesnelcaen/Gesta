<div class="p-4 bg-white rounded shadow mt-6">

    {{-- Bloc principal côte à côte --}}
    <div class="flex flex-col md:flex-row gap-4">

        {{-- Contenu (membres + formulaire) 70% --}}
        <div class="md:basis-7/12 flex-shrink-0">
            
            @if ($message)
                <div class="mb-4 text-green-600">
                    {{ $message }}
                </div>
            @endif

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
                    <li>{{ $user->name }} ({{ $user->email }})</li>
                @endforeach
            </ul>

        </div>

        {{-- Lottie 30% --}}
        <div class="md:basis-5/12 flex justify-center items-center flex-shrink-0">
            <div id="lottie-container" style="width:100%; max-width:300px;"></div>
        </div>

    </div>

    {{-- Animation Lottie --}}
    <script>
        const animation = lottie.loadAnimation({
            container: document.getElementById('lottie-container'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '{{ asset("animations/team work.json") }}'
        });
    </script>

</div>
