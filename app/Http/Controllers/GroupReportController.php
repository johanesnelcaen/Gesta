<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GroupReportController extends Controller
{
    public function show($groupId)
    {
        return view('groups.report', compact('groupId'));
    }

    public function exportPDF($groupId)
    {
        $group = Group::with(['owner', 'tasks.assignedUser'])->findOrFail($groupId);

        $tasks = $group->tasks;
        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('is_completed', true)->count();
        $pendingTasks = $tasks->where('is_completed', false)->count();
        $overdueTasks = $tasks->where('is_completed', false)
            ->filter(fn($task) => Carbon::parse($task->end)->isPast())
            ->count();

        $pdf = Pdf::loadView('pdf.group-report', compact(
            'group',
            'tasks',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'overdueTasks'
        ));

        return $pdf->download("rapport-groupe-{$group->id}.pdf");
    }
}
