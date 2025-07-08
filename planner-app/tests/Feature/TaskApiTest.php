<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\ValueObjects\Task\TaskPriority;
use App\ValueObjects\Task\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_task_api()
    {
        $this->getJson('/api/tasks')->assertUnauthorized();
        $this->postJson('/api/tasks', [])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_own_tasks()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user, 'user')->create();

        $this->actingAs($user)
            ->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonFragment(['title' => $task->title]);
    }

    public function test_user_cannot_see_others_tasks()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Task::factory()->for($other, 'user')->create(['title' => 'Hidden']);

        $this->actingAs($user)
            ->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonMissing(['title' => 'Hidden']);
    }

    public function test_can_create_root_task()
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'My Task',
            'description' => 'Example',
            'priority' => TaskPriority::P3->value,
        ];

        $this->actingAs($user)
            ->postJson('/api/tasks', $data)
            ->assertCreated()
            ->assertJsonFragment(['title' => 'My Task']);
    }

    public function test_cannot_create_task_with_missing_fields()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/tasks', [])
            ->assertStatus(422);
    }

    public function test_can_edit_own_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user, 'user')->create();

        $this->actingAs($user)
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Updated',
                'description' => 'Updated desc',
                'priority' => TaskPriority::P2->value,
            ])
            ->assertOk()
            ->assertJsonFragment(['title' => 'Updated']);
    }

    public function test_user_cannot_edit_others_task()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->for($other, 'user')->create();

        $this->actingAs($user)
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Hack!',
            ])
            ->assertForbidden();
    }

    public function test_can_complete_task_without_subtasks()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user, 'user')->create([
            'status' => TaskStatus::TODO,
        ]);

        $this->actingAs($user)
            ->patchJson("/api/tasks/{$task->id}/complete")
            ->assertOk()
            ->assertJsonFragment(['status' => TaskStatus::DONE->value]);
    }

    public function test_cannot_complete_task_with_incomplete_subtasks()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user, 'user')->create();
        Task::factory()->for($user, 'user')->create([
            'parent_id' => $task->id,
            'status' => TaskStatus::TODO,
        ]);

        $this->actingAs($user)
            ->patchJson("/api/tasks/{$task->id}/complete")
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Cannot complete a task with incomplete subtasks.']);
    }

    public function test_user_can_delete_own_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user, 'user')->create();

        $this->actingAs($user)
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_delete_others_task()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->for($other, 'user')->create();

        $this->actingAs($user)
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertForbidden();
    }
}
