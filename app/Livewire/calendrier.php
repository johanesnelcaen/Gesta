<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Calendrier extends Component
{
    public $tasks = [];

    public function mount()
    {
        $userId = Auth::id();

        // 1️⃣ Tâches personnelles (hors groupe)
        $personalTasks = Task::whereNull('parent_id')
            ->where('user_id', $userId)
            ->whereNull('group_id')
            ->whereNotNull('start')
            ->get();

        // 2️⃣ Tâches de groupe assignées à l'utilisateur
        $groupTasks = Task::whereNull('parent_id')
            ->where('assigned_to', $userId)
            ->whereNotNull('group_id')
            ->whereNotNull('start')
            ->get();

        // 3️⃣ Fusionner les deux collections
        $allTasks = $personalTasks->merge($groupTasks);

        // 4️⃣ Transformer en format calendrier
        $this->tasks = $allTasks->map(function ($task) {
            $isCompleted = $task->is_completed;
            $isOverdue = Carbon::parse($task->end)->isPast() && !$isCompleted;
            $color = $isCompleted ? '#38a169' : ($isOverdue ? '#e3342f' : '#3b82f6');

            return [
                'id' => $task->id,
                'text' => $task->title,
                'start' => Carbon::parse($task->start)->toIso8601String(),
                'end' => Carbon::parse($task->end)->toIso8601String(),
                'backColor' => $color,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.calendrier', [
            'tasks' => $this->tasks,
        ]);
    }
}
