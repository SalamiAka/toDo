<?php

namespace Tests\Feature;

use App\Models\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Task::query()->delete();
    }

    public function test_can_fetch_all_tasks()
    {
        $tasks = Task::factory(3)->create();

        $response = $this->get('/api/tasks');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => $tasks->toArray()
                ]);
    }

    public function test_can_create_new_task()
    {
        $taskData = [
            'title' => 'New Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => '2024-12-31'
        ];

        $response = $this->post('/api/tasks', $taskData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Task created successfully'
                ]);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    public function test_can_fetch_single_task()
    {
        $task = Task::factory()->create();

        $response = $this->get("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => $task->toArray()
                ]);
    }

    public function test_can_update_task()
    {
        $task = Task::factory()->create();
        
        $updatedData = [
            'title' => 'Updated Task Title',
            'status' => 'completed'
        ];

        $response = $this->put("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Task updated successfully'
                ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'status' => 'completed'
        ]);
    }

    public function test_can_delete_task()
    {
        $task = Task::factory()->create();

        $response = $this->delete("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Task deleted successfully'
                ]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_validates_required_fields_when_creating_task()
    {
        $response = $this->post('/api/tasks', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title']);
    }

    public function test_validates_status_field_when_updating_task()
    {
        $task = Task::factory()->create();

        $response = $this->put("/api/tasks/{$task->id}", [
            'status' => 'invalid_status'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
    }
} 