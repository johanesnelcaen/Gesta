<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('home');
});


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\NotificationController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques');
   Route::get('/calendrier', function () {
    return view('calendrier.index'); // Pas 'livewire.calendrier'
})->middleware(['auth'])->name('calendrier'); 
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
});
use App\Models\Task;
use Carbon\Carbon;


Route::get('/subtasks/{parentId}', function ($parentId) {
    $subtasks = Task::where('parent_id', $parentId)
        ->whereNotNull('start')
        ->get()
        ->map(function ($task) {
            $isCompleted = $task->is_completed;
            $isOverdue = Carbon::parse($task->end)->isPast() && !$isCompleted;

            $color = $isCompleted
                ? '#38a169' // Vert pour terminé
                : ($isOverdue
                    ? '#e3342f' // Rouge pour en retard
                    : '#3b82f6'); // Bleu pour en cours

            return [
                'id' => $task->id,
                'text' => $task->title,
                'start' => Carbon::parse($task->start)->toIso8601String(),
                'end' => Carbon::parse($task->end)->toIso8601String(),
                'backColor' => $color,
            ];
        });

    return response()->json($subtasks);
});

use App\Models\Group;


use App\Livewire\Groups\MyGroups;
use App\Livewire\Groups\ManageMembers;
use App\Livewire\Groups\GroupTasks;

use App\Http\Controllers\GroupTaskController;

Route::middleware(['auth'])->prefix('groups')->name('groups.')->group(function () {
    Route::get('/', MyGroups::class)->name('index');
    Route::get('/{group}/tasks', [GroupTaskController::class, 'show'])->name('tasks');
});

use App\Http\Controllers\GroupController;

Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');



Route::middleware(['auth'])->group(function () {
    Route::get('/groupes', function () {
        return view('groups.index');
    })->name('groups.index');
});



use App\Http\Controllers\TaskController;

Route::get('/taches', [TaskController::class, 'index'])
    ->middleware(['auth'])
    ->name('task-manager.index');

use App\Http\Controllers\GroupReportController;

Route::get('/groups/{group}/report', [GroupReportController::class, 'show'])
    ->name('groups.report')
    ->middleware('auth');

Route::get('/groups/{groupId}/export-pdf', [GroupReportController::class, 'exportPDF'])
    ->name('groups.export.pdf');
use App\Http\Controllers\GroupChatController;

Route::get('/groups/{group}/chat', [GroupChatController::class, 'show'])
    ->name('groups.chat')
    ->middleware('auth');
