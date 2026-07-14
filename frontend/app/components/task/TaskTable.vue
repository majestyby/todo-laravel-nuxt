<script setup lang="ts">
import type { Task } from '~/types'

defineProps<{
  tasks: Task[]
}>()

const emit = defineEmits<{
  edit: [task: Task]
  delete: [task: Task]
  statusChange: [id: number, status: string]
  sort: [field: string]
}>()

const taskStore = useTaskStore()
const authStore = useAuthStore()
const isAdmin = computed(() => authStore.user?.roles?.includes('admin') ?? false)

function sortIcon(field: string): string {
  if (taskStore.filters.sort_by !== field) return '↕'
  return taskStore.filters.sort_direction === 'asc' ? '↑' : '↓'
}
</script>

<template>
  <div class="space-y-3">
    <div class="hidden sm:flex items-center gap-4 px-5 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
      <div class="flex-1">Task</div>
      <div class="w-28 text-center cursor-pointer hover:text-gray-700 dark:hover:text-gray-200" @click="emit('sort', 'status')">
        Status {{ sortIcon('status') }}
      </div>
      <div class="w-28 text-center cursor-pointer hover:text-gray-700 dark:hover:text-gray-200" @click="emit('sort', 'due_date')">
        Due Date {{ sortIcon('due_date') }}
      </div>
      <div class="w-28 text-center cursor-pointer hover:text-gray-700 dark:hover:text-gray-200" @click="emit('sort', 'created_at')">
        Created {{ sortIcon('created_at') }}
      </div>
      <div v-if="isAdmin" class="w-20" />
    </div>

    <TaskCard
      v-for="task in tasks"
      :key="task.id"
      :task="task"
      @edit="emit('edit', $event)"
      @delete="emit('delete', $event)"
      @status-change="(id, status) => emit('statusChange', id, status)"
    />
  </div>
</template>
