<script setup lang="ts">
import type { Task, TaskFormData } from '~/types'

const props = defineProps<{
  task?: Task | null
}>()

const emit = defineEmits<{
  submit: [data: TaskFormData]
  cancel: []
}>()

const loading = ref(false)
const errors = ref<Record<string, string[]>>({})

const form = reactive<TaskFormData>({
  title: props.task?.title || '',
  description: props.task?.description || '',
  due_date: props.task?.due_date || '',
  status: props.task?.status || 'pending',
})

const isEditing = computed(() => !!props.task)

const statusOptions = [
  { label: 'Pending', value: 'pending' },
  { label: 'In Progress', value: 'in_progress' },
  { label: 'Completed', value: 'completed' },
]

async function handleSubmit() {
  loading.value = true
  errors.value = {}

  try {
    emit('submit', { ...form })
  }
  finally {
    loading.value = false
  }
}

defineExpose({ setErrors: (e: Record<string, string[]>) => { errors.value = e } })
</script>

<template>
  <form class="space-y-5" @submit.prevent="handleSubmit">
    <UFormField label="Title" :error="errors.title?.[0]" required>
      <UInput
        v-model="form.title"
        placeholder="Task title"
        size="lg"
        class="w-full"
      />
    </UFormField>

    <UFormField label="Description" :error="errors.description?.[0]">
      <UTextarea
        v-model="form.description"
        placeholder="Task description (optional)"
        :rows="3"
        class="w-full"
      />
    </UFormField>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <UFormField label="Due Date" :error="errors.due_date?.[0]">
        <UInput
          v-model="form.due_date"
          type="date"
          size="lg"
          class="w-full"
        />
      </UFormField>

      <UFormField label="Status" :error="errors.status?.[0]">
        <USelect
          v-model="form.status"
          :items="statusOptions"
          value-key="value"
          label-key="label"
          size="lg"
          class="w-full"
        />
      </UFormField>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
      <UButton
        color="neutral"
        variant="ghost"
        @click="emit('cancel')"
      >
        Cancel
      </UButton>
      <UButton
        type="submit"
        :loading="loading"
      >
        {{ isEditing ? 'Update Task' : 'Create Task' }}
      </UButton>
    </div>
  </form>
</template>
