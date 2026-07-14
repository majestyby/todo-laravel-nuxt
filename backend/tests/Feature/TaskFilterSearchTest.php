<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TaskFilterSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    private Role $adminRole;
    private Role $userRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'admin']);
        $this->userRole = Role::create(['name' => 'user']);

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        $this->admin->assignRole($this->adminRole);

        $this->user = User::factory()->create(['email' => 'user@test.com']);
        $this->user->assignRole($this->userRole);
    }

    public function test_tasks_filtering_by_status(): void
    {
        Task::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);
        Task::factory()->create(['user_id' => $this->user->id, 'status' => 'in_progress']);
        Task::factory()->create(['user_id' => $this->user->id, 'status' => 'completed']);

        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/tasks?status=in_progress');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'in_progress');
    }

    public function test_tasks_searching_by_title_and_description(): void
    {
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'Read clean code book', 'description' => 'A great book']);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'Write elegant Laravel tests', 'description' => 'Laravel feature tests']);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'Nuxt application', 'description' => 'Build clean frontend']);

        Sanctum::actingAs($this->user);

        // Search by title
        $response = $this->getJson('/api/tasks?search=clean');
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        // Search by description
        $response = $this->getJson('/api/tasks?search=feature');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Write elegant Laravel tests');
    }

    public function test_tasks_sorting_by_due_date(): void
    {
        Task::factory()->create(['user_id' => $this->user->id, 'due_date' => '2026-08-05']);
        Task::factory()->create(['user_id' => $this->user->id, 'due_date' => '2026-08-01']);
        Task::factory()->create(['user_id' => $this->user->id, 'due_date' => '2026-08-10']);

        Sanctum::actingAs($this->user);

        // Ascending
        $response = $this->getJson('/api/tasks?sort_by=due_date&sort_direction=asc');
        $response->assertStatus(200);
        $dueDatesAsc = collect($response->json('data'))->pluck('due_date')->toArray();
        $this->assertEquals(['2026-08-01', '2026-08-05', '2026-08-10'], $dueDatesAsc);

        // Descending
        $response = $this->getJson('/api/tasks?sort_by=due_date&sort_direction=desc');
        $response->assertStatus(200);
        $dueDatesDesc = collect($response->json('data'))->pluck('due_date')->toArray();
        $this->assertEquals(['2026-08-10', '2026-08-05', '2026-08-01'], $dueDatesDesc);
    }

    public function test_tasks_pagination_limits_per_page(): void
    {
        Task::factory(15)->create(['user_id' => $this->user->id]);

        Sanctum::actingAs($this->user);

        // Per page 5
        $response = $this->getJson('/api/tasks?per_page=5');
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 15);

        // Max per page limit is 100
        $response = $this->getJson('/api/tasks?per_page=150');
        $response->assertStatus(200)
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('meta.per_page', 100);
    }
}
