import { defineStore } from 'pinia'
import type { PaginationMeta, Task, TaskFilters, TaskFormData } from '~/types'

export const useTaskStore = defineStore('tasks', () => {
  const tasks = ref<Task[]>([])
  const meta = ref<PaginationMeta | null>(null)
  const loading = ref(false)
  const filters = ref<TaskFilters>({
    search: '',
    status: '',
    sort_by: 'created_at',
    sort_direction: 'desc',
    page: 1,
    per_page: 10,
  })

  const tasksApi = useTasksApi()
  const toast = useToast()

  async function fetchTasks(): Promise<void> {
    loading.value = true
    try {
      const response = await tasksApi.list(filters.value)
      tasks.value = response.data
      meta.value = response.meta
    }
    catch (error: any) {
      toast.add({ title: 'Error', description: 'Failed to load tasks.', color: 'error' })
    }
    finally {
      loading.value = false
    }
  }

  async function createTask(data: TaskFormData): Promise<Task> {
    const response = await tasksApi.create(data)
    await fetchTasks()
    toast.add({ title: 'Success', description: 'Task created successfully.', color: 'success' })
    return response.data
  }

  async function updateTask(id: number, data: Partial<TaskFormData>): Promise<Task> {
    const response = await tasksApi.update(id, data)
    const index = tasks.value.findIndex(t => t.id === id)
    if (index !== -1) {
      tasks.value[index] = response.data
    }
    toast.add({ title: 'Success', description: 'Task updated successfully.', color: 'success' })
    return response.data
  }

  async function deleteTask(id: number): Promise<void> {
    tasks.value = tasks.value.filter(t => t.id !== id)
    try {
      await tasksApi.destroy(id)
      if (meta.value) {
        meta.value.total -= 1
      }
      toast.add({ title: 'Success', description: 'Task deleted successfully.', color: 'success' })
    }
    catch {
      await fetchTasks()
      toast.add({ title: 'Error', description: 'Failed to delete task.', color: 'error' })
    }
  }

  async function updateStatus(id: number, status: string): Promise<void> {
    const task = tasks.value.find(t => t.id === id)
    if (!task) return

    const oldStatus = task.status
    const oldLabel = task.status_label
    task.status = status as Task['status']

    try {
      const response = await tasksApi.update(id, { status: status as Task['status'] })
      const index = tasks.value.findIndex(t => t.id === id)
      if (index !== -1) {
        tasks.value[index] = response.data
      }
    }
    catch {
      task.status = oldStatus
      task.status_label = oldLabel
      toast.add({ title: 'Error', description: 'Failed to update status.', color: 'error' })
    }
  }

  function setFilters(newFilters: Partial<TaskFilters>): void {
    filters.value = { ...filters.value, ...newFilters, page: 1 }
    fetchTasks()
  }

  function setPage(page: number): void {
    filters.value.page = page
    fetchTasks()
  }

  function setSort(field: string): void {
    if (filters.value.sort_by === field) {
      filters.value.sort_direction = filters.value.sort_direction === 'asc' ? 'desc' : 'asc'
    }
    else {
      filters.value.sort_by = field
      filters.value.sort_direction = 'desc'
    }
    filters.value.page = 1
    fetchTasks()
  }

  return {
    tasks,
    meta,
    loading,
    filters,
    fetchTasks,
    createTask,
    updateTask,
    deleteTask,
    updateStatus,
    setFilters,
    setPage,
    setSort,
  }
})
