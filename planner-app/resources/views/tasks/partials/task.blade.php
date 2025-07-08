@php
    $collapseId = $id ?? 'task_' . $task['id'];
    $priorityColors = [
        1 => 'bg-orange-500 text-white',
        2 => 'bg-orange-400 text-white',
        3 => 'bg-orange-300 text-dark',
        4 => 'bg-orange-200 text-dark',
        5 => 'bg-orange-100 text-dark',
    ];
    $statusColors = [
        'TODO' => 'bg-danger text-white',
        'DONE' => 'bg-success text-white',
    ];
    $priority = $task['priority']->value;
    $status = strtoupper($task['status']->value);
@endphp

<div class="accordion-item">
    <h2 class="accordion-header d-flex align-items-center justify-content-between px-2"
        id="heading-{{ $collapseId }}"
        data-parent-id="{{ $task['id'] }}"
        data-parent-title="{{ $task['title'] }}">
        <div class="d-flex align-items-center flex-grow-1">
            <button class="accordion-button collapsed flex-grow-1 d-flex align-items-center border-0 bg-transparent shadow-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapse-{{ $collapseId }}" aria-expanded="false">
                <span class="badge me-2 {{ $priorityColors[$priority] ?? 'bg-secondary' }}" style="min-width: 60px;">P{{ $priority }}</span>
                <span class="badge me-3 {{ $statusColors[$status] ?? 'bg-secondary' }}" style="min-width: 80px;">{{ $status }}</span>
                <span class="text-truncate">{{ $task['title'] }}</span>
            </button>
        </div>

        <div class="dropdown ms-2">
            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                ⋮
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="#" class="dropdown-item"
                       data-bs-toggle="modal"
                       data-bs-target="#editTaskModal"
                       onclick="loadTaskIntoModal({{ json_encode($task) }})">Edit</a>
                </li>
                @if ($status !== 'DONE')
                    <li><a id="add-subtask" class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addTaskModal" onclick="prepareSubtaskModal(this)">Add Subtask</a></li>
                    <li><a href="#" class="dropdown-item" onclick="completeTask({{ $task['id'] }})">Complete</a></li>
                    <li><a href="#" class="dropdown-item text-danger" onclick="deleteTask({{ $task['id'] }})">Delete</a></li>
                @endif
            </ul>
        </div>
    </h2>


    <div id="collapse-{{ $collapseId }}" class="accordion-collapse collapse">
        <div class="accordion-body">
            @if (!empty($task['description']))
                <p class="mb-2">{{ $task['description'] }}</p>
                <hr>
            @endif

            @if (!$task['subtasks']->isEmpty())
                <div class="accordion ms-3">
                    @foreach ($task['subtasks'] as $i => $subtask)
                        @include('tasks.partials.task', ['task' => $subtask, 'id' => $collapseId.'_sub_'.$i])
                    @endforeach
                </div>
            @else
                <span class="text-muted">No subtasks.</span>
            @endif
        </div>

    </div>
</div>
