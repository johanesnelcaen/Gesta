<div class="relative" x-data="{ open: false }">
    <!-- Bouton cloche -->
    <button @click="open = !open" class="relative p-2 rounded-full bg-gray-100 hover:bg-gray-200">
        🔔
        @if($count > 0)
            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs px-1.5 rounded-full">
                {{ $count }}
            </span>
        @endif
    </button>

    <!-- Dropdown notifications -->
    <div x-show="open" @click.away="open = false" 
         class="absolute right-0 mt-2 w-80 bg-white border rounded-lg shadow-lg z-50">

        @forelse($notifications as $notif)
            <div class="p-3 border-b hover:bg-gray-50">
                <p class="text-sm">
                    <strong>{{ $notif->message->user->name }}</strong>
                    t’a mentionné dans 
                    <em>{{ $notif->message->group->name }}</em> :
                </p>
                <p class="text-gray-700 text-sm mt-1">"{{ $notif->message->message }}"</p>

                <button wire:click="markAsRead({{ $notif->id }})" 
                        class="mt-2 text-xs text-blue-600 hover:underline">
                    ✅ Marquer comme lu
                </button>
            </div>
        @empty
            <div class="p-4 text-gray-500 text-center">
                Aucune notification
            </div>
        @endforelse
    </div>
</div>
