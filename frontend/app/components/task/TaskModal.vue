<script setup lang="ts">
import type { Task, TaskFormData, ApiError } from '~/types'

const props = defineProps<{
  task?: Task | null
}>()

const model = defineModel<boolean>({ default: false })

const emit = defineEmits<{
  saved: []
}>()

const taskStore = useTaskStore()
const toast = useToast()
const formRef = ref<{ setErrors: (e: Record<string, string[]>) => void } | null>(null)

const title = computed(() => props.task ? 'Edit Task' : 'New Task')

async function handleSubmit(data: TaskFormData) {
  try {
    if (props.task) {
      await taskStore.updateTask(props.task.id, data)
    }
    else {
      await taskStore.createTask(data)
    }
    model.value = false
    emit('saved')
  }
  catch (error: any) {
    const apiError = error?.data as ApiError | undefined
    if (apiError?.errors && formRef.value) {
      formRef.value.setErrors(apiError.errors)
    }
    else {
      toast.add({
        title: 'Error',
        description: apiError?.message || 'Something went wrong.',
        color: 'error',
      })
    }
  }
}
</script>

<template>
  <UModal v-model:open="model">
    <template #content>
      <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">
          {{ title }}
        </h2>
        <TaskForm
          ref="formRef"
          :task="task"
          @submit="handleSubmit"
          @cancel="model = false"
        />
      </div>
    </template>
  </UModal>
</template>
