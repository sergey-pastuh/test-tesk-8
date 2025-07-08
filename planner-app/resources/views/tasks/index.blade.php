@extends('layouts.app')

@section('content')
    @auth
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold mb-0">Your Tasks</h2>
            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addTaskModal">+ Add Task</button>
        </div>

        <form method="GET" class="d-flex gap-2 mb-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search tasks..." value="{{ request('search') }}">
            <select name="status" class="form-select form-select-sm">
                <option value="" disabled selected hidden>All Statuses</option>
                <option value="todo" @selected(request('status') === 'todo')>To Do</option>
                <option value="done" @selected(request('status') === 'done')>Done</option>
            </select>
            <select name="priority" class="form-select form-select-sm">
                <option value="" disabled selected hidden>All Priorities</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" @selected(request('priority') == $i)>Priority {{ $i }}</option>
                @endfor
            </select>
            <select name="sort_priority" class="form-select form-select-sm">
                <option value="" disabled selected hidden>Sort by Priority</option>
                <option value="asc" @selected(request('sort_priority') === 'asc')>Priority ↑</option>
                <option value="desc" @selected(request('sort_priority') === 'desc')>Priority ↓</option>
            </select>

            <select name="sort_date" class="form-select form-select-sm">
                <option value="" disabled selected hidden>Sort by Date</option>
                <option value="asc" @selected(request('sort_date') === 'asc')>Oldest First</option>
                <option value="desc" @selected(request('sort_date') === 'desc')>Newest First</option>
            </select>
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            <button class="btn btn-sm btn-primary" type="submit">Apply</button>
        </form>

        @if($tasks->isEmpty())
            <div class="alert alert-warning mt-4">No tasks available.</div>
        @else
            <div class="accordion" id="taskAccordion">
                @foreach($tasks as $index => $task)
                    @include('tasks.partials.task', ['task' => $task, 'id' => 'task_'.$index])
                @endforeach
            </div>
            <div class="mt-4">
                {{ $tasks->links() }}
            </div>
        @endif
    @endauth

    @include('tasks.partials.add-modal')
    @include('tasks.partials.edit-modal')
@endsection
