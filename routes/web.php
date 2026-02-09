<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\{
    StatistiqueController,
    CalendrierController,
    NotificationController,
    GroupController,
    GroupTaskController,
    TaskController,
    GroupReportController,
    GroupChatController
};
use App\Livewire\Groups\MyGroups;
use App\Models\Task;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Dashboard & Home
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/', fn() => view('dashboard'))->name('home');
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Settings (Livewire / Volt)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('settings')->group(function () {
    Route::redirect('/', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

/*
|--------------------------------------------------------------------------
| Statistiques, Calendrier & Notifications
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques');
    Route::get('/calendrier', fn() => view('calendrier.index'))->name('calendrier');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
});

/*
|--------------------------------------------------------------------------
| Subtasks JSON API
|--------------------------------------------------------------------------
*/
Route::get('/subtasks/{parentId}', function ($parentId) {
    $subtasks = Task::where('parent_id', $parentId)
        ->whereNotNull('start')
        ->get()
        ->map(function ($task) {
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
        });

    return response()->json($subtasks);
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Groups (Livewire + Controller)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('groups')->name('groups.')->group(function () {
    // Livewire: liste des groupes
    Route::get('/', MyGroups::class)->name('index');

    // Controller: afficher les tâches d’un groupe
    Route::get('/{group}/tasks', [GroupTaskController::class, 'show'])->name('tasks');

    // Controller: rapport d’un groupe
    Route::get('/{group}/report', [GroupReportController::class, 'show'])->name('report');
    Route::get('/{group}/export-pdf', [GroupReportController::class, 'exportPDF'])->name('export.pdf');

    // Controller: chat du groupe
    Route::get('/{group}/chat', [GroupChatController::class, 'show'])->name('chat');
});

// Controller: afficher un groupe individuel (si nécessaire)
Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');

/*
|--------------------------------------------------------------------------
| Tasks
|--------------------------------------------------------------------------
*/
Route::get('/taches', [TaskController::class, 'index'])
    ->middleware('auth')
    ->name('task-manager.index');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
