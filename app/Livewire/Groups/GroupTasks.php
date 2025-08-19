<?php

namespace App\Livewire\Groups;

use Livewire\Component;
use App\Models\Group;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class GroupTasks extends Component
{
    /**
     * @var \App\Models\Group
     */
    public $group;

    /**
     * @var Collection|Task[]
     */
    public $tasks;

    public $title;
    public $assigned_to;
    public $start;
    public $end;
    public $successMessage;
    public $editingTaskId = null;

    public $totalTasks = 0;
    public $completedTasks = 0;
    public $pendingTasks = 0;

    public function mount($groupId)
    {
        $this->group = Group::with('users')->findOrFail($groupId);
        $this->loadTasks();
    }

    /**
     * Charger les tâches et calculer les stats
     */
    private function loadTasks(): void
    {
        $this->tasks = $this->group->tasks()
            ->with('assignedUser')
            ->latest()
            ->get();

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
    }

    public function createTask(): void
    {
        $this->authorizeOwner();

        $this->validate([
            'title' => 'required|string|max:255',
            'assigned_to' => 'required|exists:users,id',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        if ($this->editingTaskId) {
            $this->updateTask();
        } else {
            Task::create([
                'group_id' => $this->group->id,
                'title' => $this->title,
                'assigned_to' => $this->assigned_to,
                'is_completed' => false,
                'user_id' => Auth::id(),
                'start' => $this->start,
                'end' => $this->end,
            ]);

            $this->successMessage = "Tâche ajoutée avec succès.";
        }

        $this->reset(['title', 'assigned_to', 'start', 'end']);
        $this->loadTasks();
    }

    private function updateTask(): void
    {
        $task = Task::findOrFail($this->editingTaskId);
        $task->update([
            'title' => $this->title,
            'assigned_to' => $this->assigned_to,
            'start' => $this->start,
            'end' => $this->end,
        ]);
        $this->successMessage = "Tâche modifiée avec succès.";
        $this->editingTaskId = null;
    }

    public function editTask(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        $this->editingTaskId = $taskId;
        $this->title = $task->title;
        $this->assigned_to = $task->assigned_to;
        $this->start = $task->start;
        $this->end = $task->end;
    }

    public function deleteTask(int $taskId): void
    {
        $this->authorizeOwner();
        Task::destroy($taskId);
        $this->loadTasks();
    }

    private function authorizeOwner(): void
    {
        if (Auth::id() !== $this->group->owner_id) {
            abort(403, 'Accès refusé');
        }
    }

    public function render()
    {
        return view('livewire.groups.group-tasks', [
            'members' => $this->group->users,
            'tasks' => $this->tasks,
        ]);
    }
}
