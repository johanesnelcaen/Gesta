<div class="p-4 bg-white rounded shadow">
     <div id="lottie-container" style=" width: 50%;
    max-width: 400px;
    margin: auto;"></div>
    @if (session()->has('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="create" class="space-y-4">
        <div>
            <label class="block mb-1">Nom du groupe</label>
            <input type="text" wire:model="name" class="w-full border rounded px-3 py-2" placeholder="Ex : Équipe Marketing">
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Créer le groupe
        </button>
    </form>
     <script>
        // Charger l'animation Lottie depuis un lien JSON public
        const animation = lottie.loadAnimation({
            container: document.getElementById('lottie-container'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '{{ asset("animations/Business team.json") }}'
        });
    </script>
</div>
