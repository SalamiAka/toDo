<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_tasks()
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'status', 'priority', 'due_date', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_can_create_task()
    {
        $taskData = [
            'title' => 'Nouvelle tâche',
            'description' => 'Description de la tâche',
            'status' => 'pending',
            'priority' => 'medium'
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment($taskData);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    public function test_can_update_task()
    {
        $task = Task::factory()->create();

        $updatedData = [
            'title' => 'Tâche mise à jour',
            'description' => 'Description mise à jour',
            'status' => 'completed',
            'priority' => 'high'
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment($updatedData);

        $this->assertDatabaseHas('tasks', array_merge(['id' => $task->id], $updatedData));
    }

    public function test_can_delete_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_cannot_create_task_without_title()
    {
        $response = $this->postJson('/api/tasks', [
            'description' => 'Description sans titre',
            'status' => 'pending'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }
    
    public function test_can_get_single_task()
    {
        $task = Task::factory()->create();
        
        $response = $this->getJson("/api/tasks/{$task->id}");
        
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $task->id,
                'title' => $task->title
            ]);
    }
    
    public function test_returns_404_for_non_existent_task()
    {
        $response = $this->getJson("/api/tasks/999");
        
        $response->assertStatus(404);
    }
}
