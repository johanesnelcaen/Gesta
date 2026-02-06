<div class="p-4 bg-red-50 border border-red-200 rounded shadow-md">
    <ul class="space-y-4">
        @foreach ($tasks as $task)
            @php
                $isCompleted = $task->is_completed;
                $isOverdue = \Carbon\Carbon::parse($task->end)->isPast() && !$isCompleted;
                $color = $isCompleted ? 'bg-green-500' : ($isOverdue ? 'bg-red-500' : 'bg-blue-500');
            @endphp
            <li class="p-4 border rounded bg-white shadow-sm">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        {{-- Chevron uniquement pour les tâches parentes --}}
                        @if ($task->subtasks && $task->subtasks->count() > 0)
                            <button wire:click="toggleSubtasks({{ $task->id }})" class="text-gray-600 hover:text-gray-800">
                                @if (in_array($task->id, $expandedTasks))
                                    &#9660; {{-- chevron bas --}}
                                @else
                                    &#9658; {{-- chevron droit --}}
                                @endif
                            </button>
                        @endif

                        <div class="w-full">
                            <h2 class="text-lg font-semibold">{{ $task->title }}</h2>

                            {{-- Barre de progression --}}
                            <div class="w-full bg-gray-200 rounded-full h-5 mt-2 relative">
                                <div class="{{ $color }} h-5 rounded-full flex items-center justify-center text-white font-bold"
                                     style="width: {{ $task->progress ?? 0 }}%">
                                    {{ $task->progress ?? 0 }}%
                                </div>
                            </div>

                            <p class="text-sm text-gray-600 mt-1">
                                Date de fin : {{ \Carbon\Carbon::parse($task->end)->format('d/m/Y H:i') }}
                            </p>
                            <p class="text-sm text-blue-700">
                                @if(!$isCompleted)
                                    @php
                                        $end = \Carbon\Carbon::parse($task->end);
                                        $now = \Carbon\Carbon::now();
                                        $diff = $now->diff($end);
                                    @endphp
                                    @if ($now > $end)
                                        <span class="text-red-500 font-bold">⚠️ En retard de : </span>
                                        <span class="text-red-600">
                                            {{ $diff->y ? $diff->y . ' an(s) ' : '' }}
                                            {{ $diff->m ? $diff->m . ' mois ' : '' }}
                                            {{ $diff->d ? $diff->d . ' jour(s) ' : '' }}
                                            {{ $diff->h ? $diff->h . 'h ' : '' }}
                                            {{ $diff->i ? $diff->i . 'min ' : '' }}
                                        </span>
                                    @else
                                        ⏳ Temps restant :
                                        {{ $diff->y ? $diff->y . ' an(s) ' : '' }}
                                        {{ $diff->m ? $diff->m . ' mois ' : '' }}
                                        {{ $diff->d ? $diff->d . ' jour(s) ' : '' }}
                                        {{ $diff->h ? $diff->h . 'h ' : '' }}
                                        {{ $diff->i ? $diff->i . 'min ' : '' }}
                                    @endif
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Sous-tâches affichées si déployées --}}
                @if (in_array($task->id, $expandedTasks) && $task->subtasks)
                    <ul class="ml-6 mt-4 space-y-2">
                        @foreach ($task->subtasks as $subtask)
                            @php
                                $isCompleted = $subtask->is_completed;
                                $isOverdue = \Carbon\Carbon::parse($subtask->end)->isPast() && !$isCompleted;
                                $color = $isCompleted ? 'bg-green-500' : ($isOverdue ? 'bg-red-500' : 'bg-blue-500');
                            @endphp
                            <li class="p-2 border-l-4 border-blue-400 bg-gray-50 rounded">
                                <strong>{{ $subtask->title }}</strong><br>
                                <div class="w-full bg-gray-200 rounded-full h-3 mt-1 relative">
                                    <div class="{{ $color }} h-3 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                         style="width: {{ $subtask->progress ?? 0 }}%">
                                        {{ $subtask->progress ?? 0 }}%
                                    </div>
                                </div>
                                <small>
                                    Fin : {{ \Carbon\Carbon::parse($subtask->end)->format('d/m/Y H:i') }} <br>
                                    @if(!$isCompleted)
                                        @php
                                            $subEnd = \Carbon\Carbon::parse($subtask->end);
                                            $subNow = \Carbon\Carbon::now();
                                            $subDiff = $subNow->diff($subEnd);
                                        @endphp
                                        @if ($subNow->gt($subEnd))
                                            <span class="text-red-500 font-bold">⚠️ En retard de : </span>
                                            <span class="text-red-600">
                                                {{ $subDiff->d ? $subDiff->d . ' j ' : '' }}
                                                {{ $subDiff->h ? $subDiff->h . 'h ' : '' }}
                                                {{ $subDiff->i ? $subDiff->i . 'min' : '' }}
                                            </span>
                                        @else
                                            ⏳ 
                                            {{ $subDiff->d ? $subDiff->d . ' j ' : '' }}
                                            {{ $subDiff->h ? $subDiff->h . 'h ' : '' }}
                                            {{ $subDiff->i ? $subDiff->i . 'min' : '' }}
                                        @endif
                                    @endif
                                </small>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>