<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_tasks_page_but_sees_login_warning()
    {
        $response = $this->get('/tasks');

        $response->assertOk();
        $response->assertSee('Please login/register to view your task list');
        $response->assertDontSee('Your Tasks');
    }

    public function test_authenticated_user_can_view_tasks_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/tasks');

        $response->assertOk();
        $response->assertSee('Your Tasks');
        $response->assertSee('+ Add Task');
    }

    public function test_authenticated_user_sees_their_tasks()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user, 'user')->create(['title' => 'My Task']);

        $this->actingAs($user);
        $response = $this->get('/tasks');

        $response->assertOk();
        $response->assertSee('My Task');
    }

    public function test_authenticated_user_does_not_see_others_tasks()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Task::factory()->for($other, 'user')->create(['title' => 'Secret Task']);

        $this->actingAs($user);
        $response = $this->get('/tasks');

        $response->assertOk();
        $response->assertDontSee('Secret Task');
    }

    public function test_sorting_tasks_works()
    {
        $user = User::factory()->create();
        Task::factory()->for($user, 'user')->create(['title' => 'Oldest', 'created_at' => now()->subDays(3)]);
        Task::factory()->for($user, 'user')->create(['title' => 'Newest', 'created_at' => now()]);

        $this->actingAs($user);
        $response = $this->get('/tasks?sort_date=desc');

        $response->assertOk();
        $response->assertSeeInOrder(['Newest', 'Oldest']);
    }
}
