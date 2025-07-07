<?php

namespace App\ValueObjects\Task;

final class TaskFilterDTO
{
    public function __construct(
        public readonly ?TaskStatus $status = null,
        public readonly ?TaskPriority $priority = null,
        public readonly ?string $search = null,
        public readonly array $sort = [], // ['priority desc', 'createdAt asc']
    ) {}
}
