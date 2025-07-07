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

        //Another option of recursive implementation made with Recursive Eloquent Relationship,
        //was commented out in case if manual recursion implementation was intended in the test task
        //$query->with('allsubtasks');

        $paginated = $query->paginate(10);

        //Fetch all subtasks for parents
        $allSubtasks = Task::where('user_id', $userId)
            ->whereIn('parent_id', $paginated->pluck('id'))
            ->get();

        $grouped = $allSubtasks->groupBy('parent_id');

        //Recursively get all child subtasks on all necessary levels
        $paginated->getCollection()->transform(function (Task $task) use ($grouped) {
            return $this->buildTaskTree($task, $grouped);
        });

        return $paginated;
    }

    /**
     * @param Task $task
     * @param Collection $grouped
     * @return array
     */
    private function buildTaskTree(Task $task, Collection $grouped): array
    {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'priority' => $task->priority,
            'completed_at' => $task->completed_at,
            'created_at' => $task->created_at,
            'subtasks' => ($grouped[$task->id] ?? collect())->map(function (Task $subtask) use ($grouped) {
                return $this->buildTaskTree($subtask, $grouped);
            })->values()->toArray(),
        ];
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
            $query->where(function ($q) use ($dto) {
                $q->where('title', 'like', '%' . $dto->search . '%')
                    ->orWhere('description', 'like', '%' . $dto->search . '%');
            });
        }
    }

    /**
     * @param Builder $query
     * @param TaskFilterDTO $dto
     * @return void
     */
    private function applySorting(Builder $query, TaskFilterDTO $dto): void
    {
        foreach ($dto->sort as $clause) {
            if (!str_contains($clause, ' ')) {
                continue;
            }

            [$field, $direction] = explode(' ', trim($clause));
            $query->orderBy($field, $direction);
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
