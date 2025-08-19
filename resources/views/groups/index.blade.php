<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <!-- Bouton retour stylé -->
                <a href="{{ url()->previous() }}" 
                   class="flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded shadow-sm transition">
                    <!-- Icône flèche gauche -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.293 16.293a1 1 0 010 1.414l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L8.414 10l5.293 5.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                </a>

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
