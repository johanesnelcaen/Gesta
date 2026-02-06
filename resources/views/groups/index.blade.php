<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Groupes') }}
                </h2>
            </div>

            <div>
                <livewire:mention-notifications />
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                {{-- Formulaire de création de groupe --}}
                <livewire:groups.create-group />

                {{-- Liste des groupes --}}
                <livewire:groups.my-groups />
            </div>
        </div>
    </div>
</x-app-layout>
