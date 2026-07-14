<script setup lang="ts">
const taskStore = useTaskStore()
const search = ref(taskStore.filters.search || '')

const debouncedSearch = useDebounceFn((value: string) => {
  taskStore.setFilters({ search: value })
}, 400)

watch(search, (value) => {
  debouncedSearch(value)
})

// Синхронизируем локальный ввод при внешних изменениях (например, история браузера или сброс)
watch(
  () => taskStore.filters.search,
  (newValue) => {
    const target = newValue || ''
    if (search.value !== target) {
      search.value = target
    }
  }
)
</script>

<template>
  <UInput
    v-model="search"
    placeholder="Search tasks..."
    icon="i-heroicons-magnifying-glass"
    size="md"
    class="w-full sm:w-80"
  >
    <template #trailing>
      <UButton
        v-if="search"
        color="neutral"
        variant="ghost"
        icon="i-heroicons-x-mark"
        size="sm"
        @click="search = ''"
      />
    </template>
  </UInput>
</template>
