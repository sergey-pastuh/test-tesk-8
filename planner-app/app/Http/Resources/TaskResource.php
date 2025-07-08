<?php

namespace App\Http\Resources;

use App\Models\Task;
use App\ValueObjects\Task\TaskPriority;
use App\ValueObjects\Task\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'priority' => $this->priority->value,
            'created_at' => $this->created_at,
            'completed_at' => $this->completed_at,
            'subtasks' => TaskResource::collection($this->whenLoaded('allSubtasks') ??
                ($this->whenLoaded('subtasks') ?? [])),
        ];
    }
}
