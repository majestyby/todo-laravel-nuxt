import type {ApiResponse, PaginatedResponse, Task, TaskFilters, TaskFormData} from "~/types";

export function useTasksApi() {
  const { get, post, patch, del } = useApiFetch()

  return {
    list(filters: TaskFilters = {}) {
      return get<PaginatedResponse<Task>>('/tasks', filters as Record<string, any>)
    },

    show(id: number) {
      return get<ApiResponse<Task>>(`/tasks/${id}`)
    },

    create(data: TaskFormData) {
      return post<ApiResponse<Task>>('/tasks', data)
    },

    update(id: number, data: Partial<TaskFormData>) {
      return patch<ApiResponse<Task>>(`/tasks/${id}`, data)
    },

    destroy(id: number) {
      return del<void>(`/tasks/${id}`)
    },
  }
}
