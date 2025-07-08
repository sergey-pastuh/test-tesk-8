<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use App\ValueObjects\Task\TaskFilterDTO;
use App\ValueObjects\Task\TaskStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function __construct(private TaskRepository $repository) {}

    /**
     * @param User $user
     * @param TaskFilterDTO $dto
     * @return LengthAwarePaginator
     */
    public function getTaskTreeForUser(User $user, TaskFilterDTO $dto): LengthAwarePaginator
    {
        return $this->repository->getPaginatedTaskTreeForUser($user->id, $dto);
    }

    /**
     * @param User $user
     * @param array $data
     * @return Task
     */
    public function createTask(User $user, array $data): Task
    {
        $data['user_id'] = $user->id;
        $data['status'] = $data['status'] ?? TaskStatus::TODO->value;

        return $this->repository->create($data);
    }

    /**
     * @param Task $task
     * @param array $data
     * @return Task
     * @throws ValidationException
     */
    public function updateTask(Task $task, array $data): Task
    {
        return $this->repository->update($task, $data);
    }

    /**
     * @param Task $task
     * @return void
     * @throws ValidationException
     */
    public function deleteTask(Task $task): void
    {
        if ($task->status === TaskStatus::DONE) {
            throw ValidationException::withMessages(['status' => 'Cannot delete completed task.']);
        }

        $this->repository->delete($task);
    }

    /**
     * @param Task $task
     * @return Task
     * @throws ValidationException
     */
    public function markAsCompleted(Task $task): Task
    {
        if ($task->subtasks()->where('status', '!=', TaskStatus::DONE->value)->exists()) {
            throw ValidationException::withMessages([
                'subtasks' => 'Cannot complete a task with incomplete subtasks.',
            ]);
        }

        return $this->repository->update($task, [
            'status' => TaskStatus::DONE->value,
            'completed_at' => now(),
        ]);
    }
}
