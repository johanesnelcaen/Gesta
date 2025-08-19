<?php

namespace App\Livewire\Groups;

use Livewire\Component;
use App\Models\Group;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GroupReport extends Component
{
    /**
     * @var Group
     */
    public $group;

    /**
     * @var Collection|Task[]
     */
    public $tasks;

    public $totalTasks = 0;
    public $completedTasks = 0;
    public $pendingTasks = 0;
    public $overdueTasks = 0;

    public function mount($groupId)
    {
        $this->group = Group::with(['owner', 'tasks.assignedUser'])->findOrFail($groupId);
        $this->loadTasks();
    }

    /**
     * Charger les tâches et calculer les stats
     */
    private function loadTasks(): void
    {
        $this->tasks = $this->group->tasks()->with('assignedUser')->get();
        $this->calculateStats();
    }

    /**
     * Calculer les statistiques des tâches
     */
    private function calculateStats(): void
    {
        $this->totalTasks = $this->tasks->count();
        $this->completedTasks = $this->tasks->where('is_completed', true)->count();
        $this->pendingTasks = $this->tasks->where('is_completed', false)->count();
        $this->overdueTasks = $this->tasks
            ->where('is_completed', false)
            ->filter(fn($task) => $task->end && Carbon::parse($task->end)->isPast())
            ->count();
    }

    public function render()
    {
        return view('livewire.groups.group-report', [
            'group' => $this->group,
            'tasks' => $this->tasks,
            'totalTasks' => $this->totalTasks,
            'completedTasks' => $this->completedTasks,
            'pendingTasks' => $this->pendingTasks,
            'overdueTasks' => $this->overdueTasks,
        ]);
    }
}
