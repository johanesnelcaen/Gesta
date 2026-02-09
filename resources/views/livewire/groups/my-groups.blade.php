<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Création --}}
        <livewire:groups.create-group />

        {{-- Liste --}}
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            @include('livewire.groups.partials.groups-list')
        </div>

    </div>
</div>