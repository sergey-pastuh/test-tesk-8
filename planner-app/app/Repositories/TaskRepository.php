<?php

namespace App\Repositories;

use App\Models\Task;
use App\ValueObjects\Task\TaskFilterDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function getPaginatedTaskTreeForUser(int $userId, TaskFilterDTO $dto): LengthAwarePaginator
    {
        $query = Task::query()
            ->where('user_id', $userId)
            ->whereNull('parent_id');

        $this->applyFilters($query, $dto);
        $this->applySearch($query, $dto);
        $this->applySorting($query, $dto);

        //Recursively get all child subtasks on all necessary levels
        $query->with('allSubtasks');

        return $query->paginate(25);
    }


    /**
     * @param Builder $query
     * @param TaskFilterDTO $dto
     * @return void
     */
    private function applyFilters(Builder $query, TaskFilterDTO $dto): void
    {
        if ($dto->status) {
            $query->where('status', $dto->status->value);
        }

        if ($dto->priority) {
            $query->where('priority', $dto->priority->value);
        }
    }

    /**
     * @param Builder $query
     * @param TaskFilterDTO $dto
     * @return void
     */
    private function applySearch(Builder $query, TaskFilterDTO $dto): void
    {
        if ($dto->search) {
            $query->whereRaw('MATCH(title, description) AGAINST(? IN NATURAL LANGUAGE MODE)', [$dto->search]);
        }
    }

    /**
     * @param Builder $query
     * @param TaskFilterDTO $dto
     * @return void
     */
    private function applySorting(Builder $query, TaskFilterDTO $dto): void
    {
        foreach ($dto->sort as $order) {
            $query->orderBy($order['column'], $order['direction']);
        }
    }

    /**
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    /**
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    /**
     * @param Task $task
     * @return void
     */
    public function delete(Task $task): void
    {
        $task->delete();
    }
}
