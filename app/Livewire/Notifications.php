<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Notifications\TaskOverdueNotification;

class Notifications extends Component
{
    /**
     * @var Collection|Task[]
     */
    public $tasks;

    /**
     * @var Collection Notifications récentes
     */
    public $notifications;

    /**
     * @var int Nombre de notifications non lues
     */
    public $unreadCount = 0;

    /**
     * @var int[] IDs des tâches dont les sous-tâches sont déployées
     */
    public $expandedTasks = [];

    public function mount(): void
    {
        $this->loadTasksAndNotifications();
    }

    /**
     * Charger les tâches personnelles et de groupe, puis générer les notifications
     */
    private function loadTasksAndNotifications(): void
    {
        $user = Auth::user();
        $userId = $user->id;

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
            ->with('subtasks')
            ->get();

        // 🔹 Fusionner les deux collections
        $this->tasks = $personalTasks->merge($groupTasks);

        // 🔹 Filtrer les sous-tâches assignées à l'utilisateur
        foreach ($this->tasks as $task) {
            $task->setRelation('filteredSubtasks', $task->subtasks->filter(fn($sub) =>
                $sub->user_id === $userId || $sub->assigned_to === $userId
            ));
        }

        // 🔹 Notifications pour tâches en retard non notifiées
        foreach ($this->tasks as $task) {
            if (!$task->is_completed && $task->end && Carbon::parse($task->end)->isPast() && !$task->notified) {
                $user->notify(new TaskOverdueNotification($task));
                $task->update(['notified' => true]);
            }
        }

        // 🔹 Charger les notifications récentes
        $this->notifications = $user->notifications()->latest()->take(5)->get();
        $this->unreadCount = $user->unreadNotifications()->count();
    }

    /**
     * Déployer / replier les sous-tâches d'une tâche
     */
    public function toggleSubtasks(int $taskId): void
    {
        if (in_array($taskId, $this->expandedTasks)) {
            $this->expandedTasks = array_diff($this->expandedTasks, [$taskId]);
        } else {
            $this->expandedTasks[] = $taskId;
        }
    }

    /**
     * Récupérer les sous-tâches filtrées pour une tâche
     *
     * @return Collection
     */
    public function getSubtasks(int $taskId): Collection
    {
        $task = $this->tasks->where('id', $taskId)->first();
        return $task ? $task->filteredSubtasks : collect();
    }

    public function render()
    {
        return view('livewire.notifications', [
            'tasks' => $this->tasks,
            'notifications' => $this->notifications,
            'expanded' => $this->expandedTasks,
        ]);
    }
}
