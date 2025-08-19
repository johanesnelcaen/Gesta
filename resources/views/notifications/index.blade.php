<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Alertes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:notifications />
            <script>
    setInterval(() => {
        Livewire.emit('taskUpdated'); // CORRECT
    }, 15000);
</script>
        </div>
    </div>
</x-app-layout>
