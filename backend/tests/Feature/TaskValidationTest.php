<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TaskValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        $this->admin->assignRole($this->adminRole);
    }

    public function test_title_is_required_to_create_task(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/tasks', [
            'description' => 'Test description',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_title_must_be_at_least_three_characters(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/tasks', [
            'title' => 'ab',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_title_must_not_exceed_max_length(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/tasks', [
            'title' => str_repeat('a', 256),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_due_date_must_be_valid_date(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/tasks', [
            'title' => 'Valid Title',
            'due_date' => 'invalid-date-format',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['due_date']);
    }

    public function test_status_must_be_valid_enum(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/tasks', [
            'title' => 'Valid Title',
            'status' => 'invalid_status_enum',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }
}
