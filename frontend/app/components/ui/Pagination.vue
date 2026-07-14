<script setup lang="ts">
import type { PaginationMeta } from '~/types'

const props = defineProps<{
  meta: PaginationMeta
}>()

const emit = defineEmits<{
  pageChange: [page: number]
}>()

const pages = computed(() => {
  const total = props.meta.last_page
  const current = props.meta.current_page
  const range: number[] = []

  for (let i = Math.max(1, current - 2); i <= Math.min(total, current + 2); i++) {
    range.push(i)
  }
  return range
})
</script>

<template>
  <div class="flex items-center justify-between flex-wrap gap-4">
    <p class="text-sm text-gray-500 dark:text-gray-400">
      Showing {{ meta.from || 0 }}–{{ meta.to || 0 }} of {{ meta.total }} tasks
    </p>

    <div v-if="meta.last_page > 1" class="flex items-center gap-1">
      <UButton
        size="xs"
        color="neutral"
        variant="ghost"
        :disabled="meta.current_page === 1"
        @click="emit('pageChange', meta.current_page - 1)"
      >
        ← Prev
      </UButton>

      <UButton
        v-for="page in pages"
        :key="page"
        size="xs"
        :color="page === meta.current_page ? 'primary' : 'neutral'"
        :variant="page === meta.current_page ? 'solid' : 'ghost'"
        @click="emit('pageChange', page)"
      >
        {{ page }}
      </UButton>

      <UButton
        size="xs"
        color="neutral"
        variant="ghost"
        :disabled="meta.current_page === meta.last_page"
        @click="emit('pageChange', meta.current_page + 1)"
      >
        Next →
      </UButton>
    </div>
  </div>
</template>
