<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Clear cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole($adminRole);

        // Create regular user
        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($userRole);

        // Create tasks
        Task::factory(10)->create(['user_id' => $admin->id]);
        Task::factory(5)->create(['user_id' => $user->id]);
    }
}
