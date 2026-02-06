<?php
namespace App\Livewire;
use App\Models\Task;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class TaskManager extends Component
{
    public $title, $start, $end;
    public $tasks = [];
    public $is_project = false;
    public bool $is_urgent = false;

    public $subtaskTitle = [];
    public $subtaskStart = [];
    public $subtaskEnd = [];
    public $editingSubtaskId = null;
    public $editSubtaskData = [];
    public $showSubtaskForm = [];
    public $editingTaskId = null;
    public $editTaskData = [];
    public array $expanded = [];

    public function mount()
    {
        $this->loadTasks();
    }

    private function loadTasks()
    {
        $userId = Auth::id();

        // Tâches personnelles
        $personalTasks = Task::whereNull('parent_id')
            ->where('user_id', $userId)
            ->whereNull('group_id')
            ->with('subtasks')
            ->get();

        // Tâches de groupe assignées à l'utilisateur
        $groupTasks = Task::whereNull('parent_id')
            ->where('assigned_to', $userId)
            ->whereNotNull('group_id')
            ->with('subtasks')
            ->get();

        // Fusionner et trier
        $this->tasks = $personalTasks->merge($groupTasks)
            ->sortByDesc('is_urgent')
            ->sortBy('end');
    }

    public function addTask()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
        ]);

        auth()->user()->tasks()->create([
            'title' => $this->title,
            'start' => $this->start,
            'end' => $this->end ?? $this->start,
            'is_completed' => false,
            'is_project' => $this->is_project,
            'is_urgent' => $this->is_urgent,
        ]);

        $this->reset(['title', 'start', 'end']);
        $this->resetErrorBag();
        session()->flash('success', 'Tâche ajoutée avec succès !');
        $this->loadTasks();
    }

    public function toggleCompleted($taskId)
    {
        $task = Task::where('id', $taskId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $task->is_completed = !$task->is_completed;
        $task->save();

        $this->loadTasks();
        $this->dispatch('taskUpdated');
    }

    public function deleteTask($taskId)
    {
        $task = Task::where('id', $taskId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $task->delete();
        $this->loadTasks();
    }

    public function addSubtask($taskId)
    {
        $parent = Task::findOrFail($taskId);

        $title = $this->subtaskTitle[$taskId] ?? null;
        $start = isset($this->subtaskStart[$taskId]) ? Carbon::parse($this->subtaskStart[$taskId]) : null;
        $end = isset($this->subtaskEnd[$taskId]) ? Carbon::parse($this->subtaskEnd[$taskId]) : null;

        $this->validate([
            "subtaskTitle.$taskId" => 'required|string|max:255',
            "subtaskStart.$taskId" => 'required|date',
            "subtaskEnd.$taskId" => 'required|date|after_or_equal:subtaskStart.' . $taskId,
        ]);

        if ($start < $parent->start || $end > $parent->end) {
            $this->addError("subtaskDateRange.$taskId", "Les dates doivent être comprises entre celles de la tâche parente.");
            return;
        }

        $parent->subtasks()->create([
            'title' => $title,
            'start' => $start,
            'end' => $end,
            'user_id' => Auth::id(),
            'parent_id' => $taskId,
        ]);

        unset($this->subtaskTitle[$taskId], $this->subtaskStart[$taskId], $this->subtaskEnd[$taskId]);
        $this->resetErrorBag();
        $this->loadTasks();
    }

    public function toggleSubtaskForm($taskId)
    {
        $this->showSubtaskForm[$taskId] = !($this->showSubtaskForm[$taskId] ?? false);
    }

    public function toggleSubtasks($taskId)
    {
        if (in_array($taskId, $this->expanded)) {
            $this->expanded = array_diff($this->expanded, [$taskId]);
        } else {
            $this->expanded[] = $taskId;
        }
    }

    public function getCountdown($taskId)
    {
        $task = $this->tasks->where('id', $taskId)->first();

        if (!$task || !$task->end) {
            return null;
        }

        $now = Carbon::now();
        $end = Carbon::parse($task->end);

        if ($now->gt($end)) {
            return 'Tâche en retard';
        }

        return $now->diffForHumans($end, [
            'parts' => 3,
            'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
        ]);
    }

    public function editSubtask($subtaskId)
    {
        $subtask = Task::findOrFail($subtaskId);
        $this->editingSubtaskId = $subtaskId;

        $this->editSubtaskData = [
            'title' => $subtask->title,
            'start' => optional(Carbon::parse($subtask->start))->format('Y-m-d\TH:i'),
            'end' => optional(Carbon::parse($subtask->end))->format('Y-m-d\TH:i'),
        ];
    }

    public function updateSubtask()
    {
        $subtask = Task::findOrFail($this->editingSubtaskId);
        $parent = Task::findOrFail($subtask->parent_id);

        $this->validate([
            'editSubtaskData.title' => 'required|string|max:255',
            'editSubtaskData.start' => 'required|date',
            'editSubtaskData.end' => 'required|date|after_or_equal:editSubtaskData.start',
        ]);

        $start = Carbon::parse($this->editSubtaskData['start']);
        $end = Carbon::parse($this->editSubtaskData['end']);

        if ($start < $parent->start || $end > $parent->end) {
            $this->addError('editSubtaskData.end', 'Les dates doivent être comprises entre celles de la tâche parente.');
            return;
        }

        $subtask->update([
            'title' => $this->editSubtaskData['title'],
            'start' => $start,
            'end' => $end,
        ]);

        $this->editingSubtaskId = null;
        $this->editSubtaskData = [];
        $this->resetErrorBag();
        $this->loadTasks();
    }

    public function editTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        $this->editingTaskId = $taskId;

        $this->editTaskData = [
            'title' => $task->title,
            'start' => optional(Carbon::parse($task->start))->format('Y-m-d\TH:i'),
            'end' => optional(Carbon::parse($task->end))->format('Y-m-d\TH:i'),
        ];
    }

    public function updateTask()
    {
        $this->validate([
            'editTaskData.title' => 'required|string|max:255',
            'editTaskData.start' => 'required|date',
            'editTaskData.end' => 'required|date|after_or_equal:editTaskData.start',
        ]);

        $task = Task::findOrFail($this->editingTaskId);
        $task->update([
            'title' => $this->editTaskData['title'],
            'start' => Carbon::parse($this->editTaskData['start']),
            'end' => Carbon::parse($this->editTaskData['end']),
        ]);

        $this->editingTaskId = null;
        $this->editTaskData = [];
        $this->resetErrorBag();
        $this->loadTasks();
    }

    public function render()
    {
        return view('livewire.task-manager', [
            'tasks' => $this->tasks,
        ]);
    }
}
