<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\ValueObjects\Task\TaskPriority;
use App\ValueObjects\Task\TaskStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaskSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        //Create a demo user
        $user = User::firstOrCreate([
            'email' => 'user@example.com',
        ], [
            'name' => 'Demo User',
            'password' => Hash::make('12345678'),
        ]);

        //Seed 40 root tasks
        for ($i = 0; $i < 40; $i++) {
            $rootTask = $this->createTask($user->id);

            // eed subtasks recursively (up to 3 levels)
            $this->createSubtasks($user->id, $rootTask->id, 1);
        }
    }

    /**
     * @param int $userId
     * @param int|null $parentId
     * @return Task
     */
    private function createTask(int $userId, ?int $parentId = null): Task
    {
        $status = fake()->randomElement(TaskStatus::cases());
        $priority = fake()->randomElement(TaskPriority::cases());

        return Task::create([
            'user_id' => $userId,
            'parent_id' => $parentId,
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => $status,
            'priority' => $priority,
            'completed_at' => $status === TaskStatus::DONE ? now()->subDays(rand(1, 5)) : null,
        ]);
    }

    /**
     * @param int $userId
     * @param int $parentId
     * @param int $level
     * @return void
     */
    private function createSubtasks(int $userId, int $parentId, int $level): void
    {
        if ($level > 3) {
            return;
        }

        $count = rand(1, 3);
        for ($i = 0; $i < $count; $i++) {
            $task = $this->createTask($userId, $parentId);
            $this->createSubtasks($userId, $task->id, $level + 1);
        }
    }
}
