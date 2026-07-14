<script setup lang="ts">
const props = defineProps<{
  title?: string
  description?: string
  confirmLabel?: string
  confirmColor?: string
}>()

const model = defineModel<boolean>({ default: false })

const emit = defineEmits<{
  confirm: []
}>()
</script>

<template>
  <UModal v-model:open="model">
    <template #content>
      <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
          {{ title || 'Confirm action' }}
        </h2>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          {{ description || 'Are you sure you want to proceed? This action cannot be undone.' }}
        </p>

        <div class="flex items-center justify-end gap-3 mt-6">
          <UButton
            color="neutral"
            variant="ghost"
            @click="model = false"
          >
            Cancel
          </UButton>
          <UButton
            :color="(confirmColor as any) || 'error'"
            @click="emit('confirm'); model = false"
          >
            {{ confirmLabel || 'Confirm' }}
          </UButton>
        </div>
      </div>
    </template>
  </UModal>
</template>
