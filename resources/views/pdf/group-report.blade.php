<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport du groupe</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f2f2f2; }
        .stats { display: flex; justify-content: space-around; margin-top: 20px; }
        .stat-box { text-align: center; }
        .green { color: green; }
        .yellow { color: orange; }
        .red { color: red; }
    </style>
</head>
<body>
    <h1>Rapport du groupe : {{ $group->name }}</h1>
    <p style="text-align:center;">Propriétaire : {{ $group->owner->name }}</p>

    <div class="stats">
        <div class="stat-box">
            <div>{{ $totalTasks }}</div>
            <div>Total tâches</div>
        </div>
        <div class="stat-box green">
            <div>{{ $completedTasks }}</div>
            <div>Terminées</div>
        </div>
        <div class="stat-box yellow">
            <div>{{ $pendingTasks }}</div>
            <div>En cours</div>
        </div>
        <div class="stat-box red">
            <div>{{ $overdueTasks }}</div>
            <div>En retard</div>
        </div>
    </div>

    <h2>Détails des tâches</h2>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Assignée à</th>
                <th>Statut</th>
                <th>Dates</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->assignedUser?->name ?? 'Non assignée' }}</td>
                    <td>
                        @if($task->is_completed)
                            Terminée
                        @elseif(Carbon\Carbon::parse($task->end)->isPast())
                            En retard
                        @else
                            En cours
                        @endif
                    </td>
                    <td>{{ $task->start }} → {{ $task->end }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
