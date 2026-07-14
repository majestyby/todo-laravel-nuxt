<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    public function list(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = $user->hasRole('admin') ? Task::query() : Task::where('user_id', $user->id);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $status = TaskStatus::tryFrom($filters['status']);
            if ($status) {
                $query->where('status', $status);
            }
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        $allowedSortFields = ['due_date', 'status', 'created_at'];
        if (! in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'created_at';
        }

        $sortDirection = in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'desc';

        $query->orderBy($sortField, $sortDirection);

        $perPage = min((int) ($filters['per_page'] ?? 10), 100);

        return $query->paginate($perPage);
    }

    public function create(User $user, array $data): Task
    {
        $task = new Task($data);
        $task->user()->associate($user);
        $task->save();

        return $task;
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->fresh();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}
