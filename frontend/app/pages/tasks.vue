<script setup lang="ts">
import type { Task } from '~/types'

definePageMeta({ middleware: 'auth' })

useHead({
  title: 'Tasks',
})

const taskStore = useTaskStore()
const authStore = useAuthStore()

const isAdmin = computed(() => authStore.user?.roles?.includes('admin') ?? false)

const showModal = ref(false)
const showDeleteDialog = ref(false)
const editingTask = ref<Task | null>(null)
const deletingTask = ref<Task | null>(null)

const route = useRoute()
const router = useRouter()

const isInitialized = ref(false)

onMounted(async () => {
  // Считываем параметры из URL и инициализируем фильтры в хранилище
  const querySearch = (route.query.search as string) || ''
  const queryStatus = (route.query.status as string) || ''

  taskStore.filters.search = querySearch
  taskStore.filters.status = queryStatus as any

  isInitialized.value = true
  await taskStore.fetchTasks()
})

// Наблюдаем за изменением параметров в URL (например, при переходе назад/вперед в истории браузера)
watch(
  () => route.query,
  async (newQuery) => {
    if (!isInitialized.value) return

    const querySearch = (newQuery.search as string) || ''
    const queryStatus = (newQuery.status as string) || ''

    const currentSearch = taskStore.filters.search || ''
    const currentStatus = taskStore.filters.status || ''

    if (currentSearch !== querySearch || currentStatus !== queryStatus) {
      taskStore.filters.search = querySearch
      taskStore.filters.status = queryStatus as any
      taskStore.filters.page = 1
      await taskStore.fetchTasks()
    }
  },
  { deep: true }
)

// Наблюдаем за изменением фильтров в хранилище и синхронизируем их в URL
watch(
  () => [taskStore.filters.search, taskStore.filters.status],
  ([newSearch, newStatus]) => {
    if (!isInitialized.value) return

    const query = { ...route.query }

    if (newSearch) {
      query.search = newSearch as string
    } else {
      delete query.search
    }

    if (newStatus) {
      query.status = newStatus as string
    } else {
      delete query.status
    }

    const routeSearch = (route.query.search as string) || ''
    const routeStatus = (route.query.status as string) || ''
    const currentSearch = newSearch || ''
    const currentStatus = newStatus || ''

    if (routeSearch !== currentSearch || routeStatus !== currentStatus) {
      router.replace({ query })
    }
  }
)

function openCreate() {
  editingTask.value = null
  showModal.value = true
}

function openEdit(task: Task) {
  editingTask.value = task
  showModal.value = true
}

function openDelete(task: Task) {
  deletingTask.value = task
  showDeleteDialog.value = true
}

async function confirmDelete() {
  if (deletingTask.value) {
    await taskStore.deleteTask(deletingTask.value.id)
    deletingTask.value = null
  }
}

function handleStatusChange(id: number, status: string) {
  taskStore.updateStatus(id, status)
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        Tasks
      </h1>
      <UButton v-if="isAdmin" size="md" @click="openCreate">
        + New Task
      </UButton>
    </div>

    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-6">
      <TaskSearch />
      <TaskFilters />
    </div>

    <UiLoader v-if="taskStore.loading" />

    <template v-else-if="taskStore.tasks.length > 0">
      <TaskTable
        :tasks="taskStore.tasks"
        @edit="openEdit"
        @delete="openDelete"
        @status-change="handleStatusChange"
        @sort="taskStore.setSort"
      />

      <div v-if="taskStore.meta" class="mt-6">
        <UiPagination
          :meta="taskStore.meta"
          @page-change="taskStore.setPage"
        />
      </div>
    </template>

    <UiEmptyState
      v-else
      title="No tasks yet"
      :description="isAdmin ? 'Create your first task to get started.' : 'There are currently no tasks assigned to you.'"
      icon="📋"
    >
      <UButton v-if="isAdmin" @click="openCreate">
        Create Task
      </UButton>
    </UiEmptyState>

    <TaskModal
      v-model="showModal"
      :task="editingTask"
      @saved="editingTask = null"
    />

    <UiConfirmDialog
      v-model="showDeleteDialog"
      title="Delete Task"
      :description="`Are you sure you want to delete '${deletingTask?.title}'? This action cannot be undone.`"
      confirm-label="Delete"
      @confirm="confirmDelete"
    />
  </div>
</template>
