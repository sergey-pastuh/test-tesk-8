<?php

namespace App\ValueObjects\Task;

enum TaskPriority: int
{
    case LOW = 1;
    case MEDIUM = 3;
    case HIGH = 5;

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
        };
    }
}
