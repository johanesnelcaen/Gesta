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

        // 🔹 Calculer la progression et envoyer les notifications
        foreach ($this->tasks as $task) {
            // Calculer la progression de la tâche principale (ne pas sauvegarder)
            $task->progress = $this->getProgressPercentage($task);

            // Notification pour tâche en retard non notifiée
            if (!$task->is_completed && $task->end && Carbon::parse($task->end)->isPast() && !$task->notified) {
                $user->notify(new TaskOverdueNotification($task));
                // Mettre à jour UNIQUEMENT le champ notified via une requête directe
                Task::where('id', $task->id)->update(['notified' => true]);
                $task->notified = true; // Synchroniser l'objet en mémoire
            }

            // Calculer la progression des sous-tâches
            if ($task->subtasks) {
                foreach ($task->subtasks as $subtask) {
                    // Calculer la progression (ne pas sauvegarder)
                    $subtask->progress = $this->getProgressPercentage($subtask);
                    
                    // Notification pour sous-tâche en retard
                    if (!$subtask->is_completed && $subtask->end && Carbon::parse($subtask->end)->isPast() && !$subtask->notified) {
                        $user->notify(new TaskOverdueNotification($subtask));
                        // Mettre à jour UNIQUEMENT le champ notified via une requête directe
                        Task::where('id', $subtask->id)->update(['notified' => true]);
                        $subtask->notified = true; // Synchroniser l'objet en mémoire
                    }
                }
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
     * Calculer le pourcentage de progression basé sur les dates
     */
    private function getProgressPercentage($task): int
    {
        if (!$task->start || !$task->end) {
            return 0; // pas de dates, pas de progression
        }

        $start = Carbon::parse($task->start);
        $end = Carbon::parse($task->end);
        $now = Carbon::now();

        if ($now->gte($end)) {
            return 100; // échéance dépassée
        }

        if ($now->lte($start)) {
            return 0; // tâche pas encore commencée
        }

        $totalDuration = $end->diffInSeconds($start);
        $elapsed = $now->diffInSeconds($start);

        return round(($elapsed / $totalDuration) * 100);
    }

    public function render()
    {
        // Recalculer la progression avant chaque affichage
        foreach ($this->tasks as $task) {
            $task->progress = $this->getProgressPercentage($task);
            
            if ($task->subtasks) {
                foreach ($task->subtasks as $subtask) {
                    $subtask->progress = $this->getProgressPercentage($subtask);
                }
            }
        }
        
        return view('livewire.notifications', [
            'tasks' => $this->tasks,
            'notifications' => $this->notifications,
            'expanded' => $this->expandedTasks,
        ]);
    }
}