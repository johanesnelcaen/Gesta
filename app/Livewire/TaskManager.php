<?php



namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Carbon\Carbon;


class TaskManager extends Component
{
   public $title, $start, $end;
    public $tasks = [];
    public $is_project = false;
    public bool $is_urgent = false;

    public $subtaskTitle = [];
public $showSubtaskForm = [];
public array $expanded = [];
public $subtaskStart = [];
public $subtaskEnd = [];
public $editingSubtaskId = null;
public $editSubtaskData = [];
public bool $showForm = false;
public $editingTaskId = null;
public $editTaskData = [];





    

    public function mount()
    {
        $this->loadTasks();
    }

  private function loadTasks()
{
    $userId = Auth::id();

    // ✅ Tâches personnelles
    $personalTasks = Task::whereNull('parent_id')
        ->where('user_id', $userId)
        ->whereNull('group_id')
        ->with('subtasks')
        ->orderBy('is_urgent', 'desc')
        ->orderBy('end', 'asc')
        ->get();

    // ✅ Tâches de groupe assignées à l'utilisateur
    $groupTasks = Task::whereNull('parent_id')
        ->where('assigned_to', $userId)
        ->whereNotNull('group_id')
        ->with('subtasks')
        ->orderBy('is_urgent', 'desc')
        ->orderBy('end', 'asc')
        ->get();

    // ✅ Fusionner les deux
    $this->tasks = $personalTasks->merge($groupTasks);
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
    session()->flash('success', 'Tâche ajoutée avec succès !');
}

    public function toggleCompleted($taskId)
    {
        $task = Task::where('id', $taskId)->where('user_id', Auth::id())->firstOrFail();
        $task->is_completed = !$task->is_completed;
        $task->save();

        $this->loadTasks();
      $this->dispatch('taskUpdated');



    }

    public function deleteTask($taskId)
    {
        $task = Task::where('id', $taskId)->where('user_id', Auth::id())->firstOrFail();
        $task->delete();

        $this->loadTasks();
    }

    public function toggleForm()
{
    $this->showForm = !$this->showForm;
}

public function addSubtask($taskId)
{
    $parent = Task::findOrFail($taskId);

    $title = $this->subtaskTitle[$taskId] ?? null;
    $start = $this->subtaskStart[$taskId] ?? null;
    $end = $this->subtaskEnd[$taskId] ?? null;

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
        'user_id' => auth()->id(),
        'parent_id' => $taskId,
    ]);

    unset($this->subtaskTitle[$taskId], $this->subtaskStart[$taskId], $this->subtaskEnd[$taskId]);
}

public function toggleSubtaskForm($taskId)
{
    $this->showSubtaskForm[$taskId] = !($this->showSubtaskForm[$taskId] ?? false);
}



  public function render()
{
    return view('livewire.task-manager', [
        'tasks' => $this->tasks,
    ]);
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
        'parts' => 3, // montre jusqu’à 3 unités
        'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
    ]);
}

public function editSubtask($subtaskId)
{
    $subtask = Task::findOrFail($subtaskId); // Les sous-tâches sont aussi dans Task
    $this->editingSubtaskId = $subtaskId;
    $this->editSubtaskData = [
        'title' => $subtask->title,
        'start' => optional(\Carbon\Carbon::parse($task->start))->format('Y-m-d\TH:i'),
        'end' => optional(\Carbon\Carbon::parse($task->end))->format('Y-m-d\TH:i'),
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

    if (
        $this->editSubtaskData['start'] < $parent->start ||
        $this->editSubtaskData['end'] > $parent->end
    ) {
        $this->addError('editSubtaskData.end', 'Les dates doivent être comprises entre celles de la tâche parente.');
        return;
    }

    $subtask->update($this->editSubtaskData);
    $this->editingSubtaskId = null;
    $this->editSubtaskData = [];
}
public function editTask($taskId)
{
    $task = Task::findOrFail($taskId);

    $this->editingTaskId = $taskId;

    $this->editTaskData = [
        'title' => $task->title,
        'start' => optional(\Carbon\Carbon::parse($task->start))->format('Y-m-d\TH:i'),
        'end' => optional(\Carbon\Carbon::parse($task->end))->format('Y-m-d\TH:i'),
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
    $task->update($this->editTaskData);

    $this->editingTaskId = null;
    $this->editTaskData = [];
}



}

