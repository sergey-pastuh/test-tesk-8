<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use App\ValueObjects\Task\TaskFilterDTO;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private TaskService $taskService
    ) {}

    /**
     * Get paginated task tree for current user
     *
     * Returns a paginated list of root tasks, each with nested subtasks.
     *
     * @group Tasks
     * @authenticated
     * @queryParam status string Optional. Filter by task status. Example: todo
     * @queryParam priority integer Optional. Filter by task priority (1 to 5). Example: 3
     * @queryParam search string Optional. Search by title or description. Example: bug fix
     * @queryParam sort string Optional. Sort by multiple fields. Example: priority desc,created_at asc
     *
     * @apiResourceCollection App\Http\Resources\TaskResource
     * @apiResourceModel App\Models\Task
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $dto = TaskFilterDTO::fromRequest($request);

        $paginated = $this->taskService->getTaskTreeForUser($user, $dto);

        return TaskResource::collection($paginated);
    }

    /**
     * Create a new task
     *
     * Creates a new root-level or nested task for the authenticated user.
     *
     * @group Tasks
     * @authenticated
     * @bodyParam title string required The task title. Example: Fix login bug
     * @bodyParam description string Optional description. Example: Button doesn't submit
     * @bodyParam priority integer required Task priority (1 to 5). Example: 3
     * @bodyParam status string Optional. Default is "todo". Example: todo
     * @bodyParam parent_id integer Optional. ID of the parent task. Example: 4
     *
     * @apiResource App\Http\Resources\TaskResource
     * @apiResourceModel App\Models\Task
     */
    public function store(StoreTaskRequest $request): TaskResource
    {
        $task = $this->taskService->createTask($request->user(), $request->validated());

        return new TaskResource($task);
    }

    /**
     * Update an existing task
     *
     * Updates a task’s title, description, status, or priority. Only editable if not completed.
     *
     * @group Tasks
     * @authenticated
     * @urlParam task int required The ID of the task to update.
     * @bodyParam title string Optional. New title.
     * @bodyParam description string Optional.
     * @bodyParam priority integer Optional. Example: 2
     * @bodyParam status string Optional. Must be a valid status. Example: done
     * @bodyParam parent_id integer Optional. New parent ID.
     *
     * @apiResource App\Http\Resources\TaskResource
     * @apiResourceModel App\Models\Task
     */
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $updated = $this->taskService->updateTask($task, $request->validated());

        return new TaskResource($updated);
    }

    /**
     * Delete a task
     *
     * Deletes a task. Only allowed if the task is not completed.
     *
     * @group Tasks
     * @authenticated
     * @urlParam task int required The ID of the task to delete.
     *
     * @apiResource App\Http\Resources\TaskResource
     * @apiResourceModel App\Models\Task
     */
    public function destroy(Request $request, Task $task): TaskResource
    {
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($task);

        return new TaskResource($task);
    }

    /**
     * Mark task as completed
     *
     * Marks a task as done. Only allowed if all subtasks are completed.
     *
     * @group Tasks
     * @authenticated
     * @urlParam task int required The ID of the task to complete.
     *
     * @apiResource App\Http\Resources\TaskResource
     * @apiResourceModel App\Models\Task
     */
    public function complete(Request $request, Task $task): TaskResource
    {
        $this->authorize('complete', $task);

        $completed = $this->taskService->markAsCompleted($task);

        return new TaskResource($completed);
    }
}
