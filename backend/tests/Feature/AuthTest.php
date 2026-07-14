<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Role $userRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        $this->userRole = Role::create(['name' => 'user']);

        // Create a test user
        $this->user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);
        $this->user->assignRole($this->userRole);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'roles',
                ],
            ]);
    }

    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@test.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_logged_in_user_can_get_their_profile(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonPath('data.email', 'test@test.com');
    }

    public function test_guest_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_user_can_logout(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully.']);
    }
}
