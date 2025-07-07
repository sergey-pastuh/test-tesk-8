<?php

namespace App\ValueObjects\Task;

enum TaskStatus: string
{
    case TODO = 'todo';
    case DONE = 'done';

    public function label(): string
    {
        return match ($this) {
            self::TODO => 'To Do',
            self::DONE => 'Done',
        };
    }
}
