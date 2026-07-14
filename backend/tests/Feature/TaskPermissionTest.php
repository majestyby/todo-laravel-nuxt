<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TaskPermissionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    private Role $adminRole;
    private Role $userRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->userRole = Role::create(['name' => 'user']);

        // Create users
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
        ]);
        $this->admin->assignRole($this->adminRole);

        $this->user = User::factory()->create([
            'email' => 'user@test.com',
        ]);
        $this->user->assignRole($this->userRole);
    }

    public function test_admin_can_see_all_tasks(): void
    {
        // Create 2 tasks for admin and 3 tasks for user
        Task::factory(2)->create(['user_id' => $this->admin->id]);
        Task::factory(3)->create(['user_id' => $this->user->id]);

        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_regular_user_can_only_see_their_own_tasks(): void
    {
        // Create 2 tasks for admin and 3 tasks for user
        Task::factory(2)->create(['user_id' => $this->admin->id]);
        Task::factory(3)->create(['user_id' => $this->user->id]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_regular_user_cannot_view_admins_task(): void
    {
        $adminTask = Task::factory()->create(['user_id' => $this->admin->id]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/tasks/{$adminTask->id}");

        $response->assertStatus(403);
    }

    public function test_regular_user_cannot_create_task(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/tasks', [
            'title' => 'New Task',
            'description' => 'Description',
        ]);

        $response->assertStatus(403);
    }

    public function test_regular_user_cannot_update_task(): void
    {
        $userTask = Task::factory()->create(['user_id' => $this->user->id]);

        Sanctum::actingAs($this->user);

        $response = $this->putJson("/api/tasks/{$userTask->id}", [
            'title' => 'Updated Task',
        ]);

        $response->assertStatus(403);
    }

    public function test_regular_user_cannot_delete_task(): void
    {
        $userTask = Task::factory()->create(['user_id' => $this->user->id]);

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("/api/tasks/{$userTask->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_create_task(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/tasks', [
            'title' => 'New Admin Task',
            'description' => 'Admin Description',
            'due_date' => '2026-08-01',
            'status' => 'pending',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'New Admin Task');
    }

    public function test_admin_can_update_any_task(): void
    {
        $userTask = Task::factory()->create(['user_id' => $this->user->id]);

        Sanctum::actingAs($this->admin);

        $response = $this->putJson("/api/tasks/{$userTask->id}", [
            'title' => 'Updated by Admin',
            'status' => 'in_progress',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated by Admin');
    }

    public function test_admin_can_delete_any_task(): void
    {
        $userTask = Task::factory()->create(['user_id' => $this->user->id]);

        Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/tasks/{$userTask->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $userTask->id]);
    }
}
