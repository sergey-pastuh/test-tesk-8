<?php

namespace App\Http\Requests;

use App\ValueObjects\Task\TaskPriority;
use App\ValueObjects\Task\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTaskRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        //Handled via policy
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', new Enum(TaskPriority::class)],
            'status' => ['nullable', new Enum(TaskStatus::class)],
            'parent_id' => ['nullable', 'exists:tasks,id'],
        ];
    }

    /**
     * @return void
     */
    public function prepareForValidation(): void
    {
        if (!$this->has('status')) {
            $this->merge(['status' => TaskStatus::TODO->value]);
        }
    }
}
