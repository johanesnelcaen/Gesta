<div class="flex flex-col lg:flex-row gap-6 justify-between items-stretch p-6 w-full">


   {{-- Bloc de gauche - Statistiques des tâches --}}
<div class="w-full lg:w-1/2 bg-white rounded shadow p-6 min-h-[500px]">



    <h2 class="text-xl font-bold mb-4 text-center">📋 Statistiques des tâches</h2>
<canvas id="taskChart" class="mt-6"></canvas>
   <table class="w-full mt-6 border text-left">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2">Type</th>
            <th class="p-2">Total tâches</th>
            <th class="p-2">Terminées</th>
            <th class="p-2">En retard</th>
        </tr>
    </thead>
    <tbody>
        <tr class="border-t">
            <td class="p-2">Tâche</td>
            <td class="p-2">{{ $taskCount }}</td>
            <td class="p-2">{{ $completedTaskCount }}</td>
            <td class="p-2 text-red-600">{{ $overdueTaskCount }}</td>
        </tr>
        <tr class="border-t">
            <td class="p-2">Projet</td>
            <td class="p-2">{{ $projectCount }}</td>
            <td class="p-2">{{ $completedProjectCount }}</td>
            <td class="p-2 text-red-600">{{ $overdueProjectCount }}</td>
        </tr>
        <tr class="font-bold border-t bg-gray-50">
            <td class="p-2">Total</td>
            <td class="p-2">{{ $totalRow['total'] }}</td>
            <td class="p-2">{{ $totalRow['completed'] }}</td>
            <td class="p-2 text-red-600">{{ $totalRow['overdue'] }}</td>
        </tr>
    </tbody>
</table>

</div>


    {{-- Bloc de droite - Statistiques des sous-tâches --}}
   <div class="w-full lg:w-1/2 bg-white rounded shadow p-6 min-h-[500px]">



        <h3 class="text-xl font-bold mb-4">📊 Sous-tâches par tâche</h3>
        <canvas id="subtaskChart" height="120"></canvas>

        <table class="w-full border text-left mt-6">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">Tâche parente</th>
                    <th class="p-2">Sous-tâches</th>
                    <th class="p-2">Terminées</th>
                    <th class="p-2">En retard</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subtaskStats as $stat)
                    <tr class="border-t">
                        <td class="p-2">{{ $stat['title'] }}</td>
                        <td class="p-2">{{ $stat['total'] }}</td>
                        <td class="p-2">{{ $stat['completed'] }}</td>
                        <td class="p-2 text-red-600">{{ $stat['overdue'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-2 text-gray-500 text-center">Aucune sous-tâche enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

   <script>
     document.addEventListener('livewire:load', function () {
        setInterval(() => {
            Livewire.emit('taskUpdated');
        }, 15000);
    });
</script>


<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('taskChart').getContext('2d');

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total', 'Terminées', 'En retard'],
                datasets: [{
                    label: 'Statistiques des tâches',
                    data: [@js($totalTasks), @js($completedTasks), @js($overdueTasks)],
                    backgroundColor: [
                        '#3b82f6', // bleu
                        '#10b981', // vert
                        '#ef4444'  // rouge
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('subtaskChart').getContext('2d');

    const data = {
        labels: {!! json_encode(array_column($subtaskStats, 'title')) !!},
        datasets: [
            {
                label: 'Total',
                data: {!! json_encode(array_column($subtaskStats, 'total')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.7)'
            },
            {
                label: 'Terminées',
                data: {!! json_encode(array_column($subtaskStats, 'completed')) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.7)'
            },
            {
                label: 'En retard',
                data: {!! json_encode(array_column($subtaskStats, 'overdue')) !!},
                backgroundColor: 'rgba(239, 68, 68, 0.7)'
            }
        ]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1  // ✅ Force les entiers uniquement
                    }
                }
            }
        },
    };

    new Chart(ctx, config);
});
</script>


</div>
