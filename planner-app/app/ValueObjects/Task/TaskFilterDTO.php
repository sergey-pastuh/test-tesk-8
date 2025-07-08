<?php

namespace App\ValueObjects\Task;

use Illuminate\Http\Request;

class TaskFilterDTO
{
    public function __construct(
        public readonly ?TaskStatus $status,
        public readonly ?TaskPriority $priority,
        public readonly ?string $search,
        /** @var array<array{column: string, direction: string}> */
        public readonly array $sort
    ) {}

    public static function fromRequest(Request $request): self
    {
        $sort = [];
        if ($request->filled('sort_priority')) {
            $sort[] = ['column' => 'priority', 'direction' => $request->input('sort_priority')];
        }
        if ($request->filled('sort_date')) {
            $sort[] = ['column' => 'created_at', 'direction' => $request->input('sort_date')];
        }

        return new self(
            status: TaskStatus::tryFrom($request->input('status')),
            priority: $request->filled('priority') ? TaskPriority::tryFrom((int)$request->input('priority')) : null,
            search: $request->input('search'),
            sort: $sort
        );
    }
}
