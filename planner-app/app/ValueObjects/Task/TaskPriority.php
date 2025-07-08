<?php

namespace App\ValueObjects\Task;

enum TaskPriority: int
{
    case P1 = 1;
    case P2 = 2;
    case P3 = 3;
    case P4 = 4;
    case P5 = 5;
}
