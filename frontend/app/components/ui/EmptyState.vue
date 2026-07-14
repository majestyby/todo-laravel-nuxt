<script setup lang="ts">
defineProps<{
  title?: string
  description?: string
  icon?: string
}>()
</script>

<template>
  <div class="flex flex-col items-center justify-center py-16 text-center">
    <div class="text-5xl mb-4">
      {{ icon || '📭' }}
    </div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
      {{ title || 'No items found' }}
    </h3>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
      {{ description || 'Try adjusting your search or filters.' }}
    </p>
    <div class="mt-4">
      <slot />
    </div>
  </div>
</template>
