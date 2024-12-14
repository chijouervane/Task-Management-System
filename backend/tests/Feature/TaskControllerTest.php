<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_tasks()
    {
        // Create some sample tasks
        Task::factory()->count(10)->create();

        // Make a GET request to the tasks index
        $response = $this->getJson('/api/tasks');

        // Assert response structure and status
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'title', 'description', 'status', 'created_at', 'updated_at'
                    ]
                ],
                'meta' => [
                    'current_page', 'last_page', 'per_page', 'total'
                ],
                'links' => [
                    'first', 'last', 'prev', 'next'
                ]
            ]);

        // Assert pagination
        $this->assertCount(5, $response->json('data')); // Default per page is 5
    }

    /** @test */
    public function it_can_filter_tasks_by_status()
    {
        // Create tasks with different statuses
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'completed']);

        // Filter pending tasks
        $response = $this->getJson('/api/tasks?status=pending');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['status' => 'pending']);

        // Filter completed tasks
        $response = $this->getJson('/api/tasks?status=completed');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['status' => 'completed']);
    }

    /** @test */
    public function it_can_create_a_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'pending'
        ];

        // Make a POST request to create a task
        $response = $this->postJson('/api/tasks', $taskData);

        // Assert task was created successfully
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id', 'title', 'description', 'status'
                ]
            ])
            ->assertJson([
                'message' => 'Task created successfully',
                'data' => $taskData
            ]);

        // Assert task exists in database
        $this->assertDatabaseHas('tasks', $taskData);
    }

    /** @test */
    public function it_cannot_create_task_with_invalid_data()
    {
        // Try to create a task with invalid data
        $invalidTaskData = [
            'title' => '', // Empty title (should fail validation)
            'status' => 'invalid-status' // Invalid status
        ];

        $response = $this->postJson('/api/tasks', $invalidTaskData);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status']);
    }

    /** @test */
    public function it_can_update_a_task()
    {
        // Create a task to update
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'status' => 'pending'
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'status' => 'completed'
        ];

        // Make a PUT request to update the task
        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        // Assert task was updated successfully
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id', 'title', 'description', 'status'
                ]
            ])
            ->assertJson([
                'message' => 'Task updated successfully',
                'data' => array_merge(['id' => $task->id], $updateData)
            ]);

        // Assert task was updated in database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        // Create a task to delete
        $task = Task::factory()->create();

        // Make a DELETE request to remove the task
        $response = $this->deleteJson("/api/tasks/{$task->id}");

        // Assert task was deleted successfully
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task deleted successfully'
            ]);

        // Assert task is no longer in database
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_non_existent_task()
    {
        // Try to delete a non-existent task
        $nonExistentId = 9999;

        $response = $this->deleteJson("/api/tasks/{$nonExistentId}");

        // Assert 404 Not Found
        $response->assertStatus(404);
    }
}