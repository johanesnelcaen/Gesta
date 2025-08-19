<div class="p-6 bg-white rounded shadow">

    <h1 class="text-2xl font-bold mb-4">Rapport du groupe : {{ $group->name }}</h1>
    <a href="{{ route('groups.export.pdf', $group->id) }}" 
   class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">
    📄 Exporter en PDF
</a>


    {{-- Conteneur Flex pour Lottie + Statistiques --}}
    <div class="mt-4 flex flex-col md:flex-row items-center md:items-start gap-8">

        {{-- Lottie à gauche (30%) --}}
        <div class="md:basis-3/12 flex justify-center items-center flex-shrink-0">
            <div id="lottie-container" style="width: 100%; max-width: 180px;"></div>
        </div>

        {{-- Statistiques à droite (70%) --}}
        <div class="md:basis-9/12 mb-4 p-4 bg-gray-100 rounded shadow flex justify-around text-center">
            <div>
                <div class="text-2xl font-bold">{{ $totalTasks }}</div>
                <div class="text-sm text-gray-600">Total tâches</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-green-600">{{ $completedTasks }}</div>
                <div class="text-sm text-gray-600">Terminées</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-yellow-600">{{ $pendingTasks }}</div>
                <div class="text-sm text-gray-600">En cours</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-red-600">{{ $overdueTasks }}</div>
                <div class="text-sm text-gray-600">En retard</div>
            </div>
        </div>

    </div>

    {{-- Tableau des tâches --}}
    <div class="mt-8">
        <h2 class="text-xl font-semibold mb-2">Détails des tâches</h2>
        <table class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2 text-left">Titre</th>
                    <th class="border p-2 text-left">Assignée à</th>
                    <th class="border p-2 text-left">Statut</th>
                    <th class="border p-2 text-left">Dates</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    <tr>
                        <td class="border p-2">{{ $task->title }}</td>
                        <td class="border p-2">
                            {{ $task->assignedUser ? $task->assignedUser->name : 'Non assignée' }}
                        </td>
                        <td class="border p-2">
                            @if($task->is_completed)
                                Terminée
                            @elseif(Carbon\Carbon::parse($task->end)->isPast())
                                <span class="text-red-600 font-bold">En retard</span>
                            @else
                                En cours
                            @endif
                        </td>
                        <td class="border p-2">{{ $task->start }} → {{ $task->end }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<script>
    const animation = lottie.loadAnimation({
        container: document.getElementById('lottie-container'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: '{{ asset("animations/Completed Task Files.json") }}'
    });
</script>
