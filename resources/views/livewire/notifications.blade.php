<div class="p-4 bg-red-50 border border-red-200 rounded shadow-md">
    <ul class="space-y-4">
        @foreach ($tasks as $task)
            <li class="p-4 border rounded bg-white shadow-sm">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        {{-- Chevron uniquement pour les tâches parentes --}}
                        @if ($task->filteredSubtasks->count() > 0)
                            <button wire:click="toggleSubtasks({{ $task->id }})" class="text-gray-600 hover:text-gray-800">
                                @if (in_array($task->id, $expandedTasks))
                                    &#9660; {{-- chevron bas --}}
                                @else
                                    &#9658; {{-- chevron droit --}}
                                @endif
                            </button>
                        @endif

                        <div>
                            <h2 class="text-lg font-semibold">{{ $task->title }}</h2>
                            <p class="text-sm text-gray-600">
                                Date de fin : {{ \Carbon\Carbon::parse($task->end)->format('d/m/Y H:i') }}
                            </p>
                            <p class="text-sm text-blue-700">
                                ⏳ Temps restant :
                                @php
                                    $end = \Carbon\Carbon::parse($task->end);
                                    $now = \Carbon\Carbon::now();
                                    $diff = $now->diff($end);
                                @endphp
                                @if ($now > $end)
                                    <span class="text-red-500 font-bold"> Échéance dépassée</span>
                                @else
                                    {{ $diff->y ? $diff->y . ' an(s) ' : '' }}
                                    {{ $diff->m ? $diff->m . ' mois ' : '' }}
                                    {{ $diff->d ? $diff->d . ' jour(s) ' : '' }}
                                    {{ $diff->h ? $diff->h . 'h ' : '' }}
                                    {{ $diff->i ? $diff->i . 'min ' : '' }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Sous-tâches affichées si déployées --}}
                @if (in_array($task->id, $expandedTasks))
                    <ul class="ml-6 mt-4 space-y-2">
                        @foreach ($task->filteredSubtasks as $subtask)
                            @php
                                $isCompleted = $subtask->is_completed;
                                $isOverdue = \Carbon\Carbon::parse($subtask->end)->isPast() && !$isCompleted;
                                $color = $isCompleted ? 'text-green-600' : ($isOverdue ? 'text-red-600' : 'text-blue-600');
                            @endphp
                            <li class="p-2 border-l-4 border-blue-400 bg-gray-50 rounded {{ $color }}">
                                <strong>{{ $subtask->title }}</strong><br>
                                <small>
                                    Fin : {{ \Carbon\Carbon::parse($subtask->end)->format('d/m/Y H:i') }} <br>
                                    @if (\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($subtask->end)))
                                        <span class="text-red-500 font-bold"> Échéance dépassée</span>
                                    @else
                                        ⏳ 
                                        @php
                                            $diff = \Carbon\Carbon::now()->diff(\Carbon\Carbon::parse($subtask->end));
                                        @endphp
                                        {{ $diff->d ? $diff->d . ' j ' : '' }}{{ $diff->h ? $diff->h . 'h ' : '' }}{{ $diff->i ? $diff->i . 'min' : '' }}
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
