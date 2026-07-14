export interface User {
  id: number
  name: string
  email: string
  roles?: string[]
}

export type TaskStatus = 'pending' | 'in_progress' | 'completed'

export interface Task {
  id: number
  title: string
  description: string
  due_date: string
  status: TaskStatus
  status_label: string
  created_at: string
  updated_at: string
}

export interface TaskFormData {
  title: string
  description?: string
  due_date?: string
  status?: TaskStatus
}

export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number | null
  to: number | null
}

export interface PaginationLinks {
  first: string | null
  last: string | null
  prev: string | null
  next: string | null
}

export interface ApiResponse<T> {
  data: T
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: PaginationMeta
  links: PaginationLinks
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface LoginResponse {
  token: string
  user: User
}

export interface TaskFilters {
  search?: string
  status?: TaskStatus | null
  sort_by?: string
  sort_direction?: 'asc' | 'desc'
  page?: number
  per_page?: number
}
