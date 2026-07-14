<script setup lang="ts">
import type { Task } from '~/types'

const props = defineProps<{
  task: Task
}>()

const emit = defineEmits<{
  edit: [task: Task]
  delete: [task: Task]
  statusChange: [id: number, status: string]
}>()

const taskStore = useTaskStore()
const authStore = useAuthStore()
const isAdmin = computed(() => authStore.user?.roles?.includes('admin') ?? false)

const statusColor: Record<string, string> = {
  pending: 'warning',
  in_progress: 'info',
  completed: 'success',
}

const statusOptions = [
  { label: 'Pending', value: 'pending' },
  { label: 'In Progress', value: 'in_progress' },
  { label: 'Completed', value: 'completed' },
]

function onStatusChange(value: string) {
  emit('statusChange', props.task.id, value)
}

function formatDate(date: string | null): string {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}
</script>

<template>
  <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between gap-3">
      <div class="flex-1 min-w-0">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">
          {{ task.title }}
        </h3>
        <p v-if="task.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
          {{ task.description }}
        </p>
      </div>

      <div v-if="isAdmin" class="flex items-center gap-1 shrink-0">
        <UButton
          icon="i-heroicons-pencil-square"
          color="neutral"
          variant="ghost"
          size="xs"
          @click="emit('edit', task)"
        />
        <UButton
          icon="i-heroicons-trash"
          color="error"
          variant="ghost"
          size="xs"
          @click="emit('delete', task)"
        />
      </div>
    </div>

    <div class="mt-4 flex items-center justify-between gap-3 flex-wrap">
      <div class="flex items-center gap-3">
        <UBadge :color="(statusColor[task.status] as any)" variant="subtle" size="sm">
          {{ task.status_label }}
        </UBadge>
        <span v-if="task.due_date" class="text-xs text-gray-500 dark:text-gray-400">
          📅 {{ formatDate(task.due_date) }}
        </span>
      </div>

      <USelect
        v-if="isAdmin"
        :model-value="task.status"
        :items="statusOptions"
        value-key="value"
        label-key="label"
        size="xs"
        class="w-36"
        @update:model-value="onStatusChange"
      />
    </div>
  </div>
</template>
