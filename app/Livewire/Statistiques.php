<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Statistiques extends Component
{
    /** @var int Total des tâches */
    public $totalTasks = 0;

    /** @var int Tâches complétées */
    public $completedTasks = 0;

    /** @var int Tâches en retard */
    public $overdueTasks = 0;

    /** @var array<int, array{title: string, total: int, completed: int, overdue: int}> */
    public $subtaskStats = [];

    /** @var int */
    public $taskCount = 0;
    public $completedTaskCount = 0;
    public $overdueTaskCount = 0;

    /** @var int */
    public $projectCount = 0;
    public $completedProjectCount = 0;
    public $overdueProjectCount = 0;

    /** @var array{total: int, completed: int, overdue: int} */
    public $totalRow = [];

    /** @var Collection|Task[] */
    public $tasks;

    public function mount(): void
    {
        $this->refreshStats();
    }

    /**
     * Charger les tâches personnelles et de groupe, puis calculer toutes les statistiques
     */
    public function refreshStats(): void
    {
        $userId = Auth::id();

        // 🔹 Tâches personnelles
        $personalTasks = Task::whereNull('parent_id')
            ->where('user_id', $userId)
            ->whereNull('group_id')
            ->with('subtasks')
            ->get();

        // 🔹 Tâches de groupe assignées à l'utilisateur
        $groupTasks = Task::whereNull('parent_id')
            ->where('assigned_to', $userId)
            ->whereNotNull('group_id')
            ->with(['subtasks' => fn($q) => $q->where('user_id', $userId)->orWhere('assigned_to', $userId)])
            ->get();

        // 🔹 Fusionner les deux
        $this->tasks = $personalTasks->merge($groupTasks);

        // 🔹 Statistiques générales
        $this->totalTasks = $this->tasks->count();
        $this->completedTasks = $this->tasks->where('is_completed', true)->count();
        $this->overdueTasks = $this->tasks->where('is_completed', false)
                                    ->filter(fn($t) => $t->end && Carbon::parse($t->end)->isPast())
                                    ->count();

        // 🔹 Statistiques projets et sous-tâches
        $projects = $this->tasks->where('is_project', true)->values();
        $this->subtaskStats = [];

        foreach ($projects as $project) {
            $subtasks = $project->subtasks ?? collect();
            $this->subtaskStats[$project->id] = [
                'title' => $project->title,
                'total' => $subtasks->count(),
                'completed' => $subtasks->where('is_completed', true)->count(),
                'overdue' => $subtasks->where('is_completed', false)
                                      ->filter(fn($t) => $t->end && Carbon::parse($t->end)->isPast())
                                      ->count(),
            ];
        }

        // 🔹 Comptage individuel et projets
        $tasksOnly = $this->tasks->where('is_project', false);
        $projectsOnly = $this->tasks->where('is_project', true);

        $this->taskCount = $tasksOnly->count();
        $this->completedTaskCount = $tasksOnly->where('is_completed', true)->count();
        $this->overdueTaskCount = $tasksOnly->where('is_completed', false)
                                            ->filter(fn($t) => $t->end && Carbon::parse($t->end)->isPast())
                                            ->count();

        $this->projectCount = $projectsOnly->count();
        $this->completedProjectCount = $projectsOnly->where('is_completed', true)->count();
        $this->overdueProjectCount = $projectsOnly->where('is_completed', false)
                                                 ->filter(fn($t) => $t->end && Carbon::parse($t->end)->isPast())
                                                 ->count();

        // 🔹 Total général
        $this->totalRow = [
            'total' => $this->taskCount + $this->projectCount,
            'completed' => $this->completedTaskCount + $this->completedProjectCount,
            'overdue' => $this->overdueTaskCount + $this->overdueProjectCount,
        ];
    }

    public function render()
    {
        return view('livewire.statistiques', [
            'totalTasks' => $this->totalTasks,
            'completedTasks' => $this->completedTasks,
            'overdueTasks' => $this->overdueTasks,
            'subtaskStats' => $this->subtaskStats,
            'taskCount' => $this->taskCount,
            'completedTaskCount' => $this->completedTaskCount,
            'overdueTaskCount' => $this->overdueTaskCount,
            'projectCount' => $this->projectCount,
            'completedProjectCount' => $this->completedProjectCount,
            'overdueProjectCount' => $this->overdueProjectCount,
            'totalRow' => $this->totalRow,
        ]);
    }
}
