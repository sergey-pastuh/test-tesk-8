<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use App\ValueObjects\Task\TaskFilterDTO;
use App\ValueObjects\Task\TaskPriority;
use App\ValueObjects\Task\TaskStatus;
use Illuminate\Http\Request;

class TaskWebController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $dto = TaskFilterDTO::fromRequest($request);

        if (!$user) {
            $tasks = collect();
            session()->flash('warning', 'Please login/register to view your task list.');
        } else {
            $tasks = $this->taskService->getTaskTreeForUser($user, $dto);
        }

        return view('tasks.index', [
            'tasks' => TaskResource::collection($tasks),
        ]);
    }
}
