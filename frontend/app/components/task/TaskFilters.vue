<script setup lang="ts">
import type { TaskStatus } from '~/types'

const taskStore = useTaskStore()

const statusOptions = [
  { label: 'All statuses', value: null },
  { label: 'Pending', value: 'pending' },
  { label: 'In Progress', value: 'in_progress' },
  { label: 'Completed', value: 'completed' },
]

const selectedStatus = ref(taskStore.filters.status || null)

watch(selectedStatus, (value) => {
  taskStore.setFilters({ status: value as TaskStatus })
})

// Синхронизируем локальный выбор при внешних изменениях (например, история браузера или сброс)
watch(
  () => taskStore.filters.status,
  (newValue) => {
    const target = newValue || null
    if (selectedStatus.value !== target) {
      selectedStatus.value = target
    }
  }
)
</script>

<template>
  <USelect
    v-model="selectedStatus"
    :items="statusOptions"
    value-key="value"
    label-key="label"
    placeholder="All statuses"
    size="md"
    class="w-full sm:w-48"
  />
</template>
