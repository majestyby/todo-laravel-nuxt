import { describe, it, expect, beforeEach, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useTaskStore } from './tasks'

describe('Tasks Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  it('initializes with default filters and empty tasks list', () => {
    const store = useTaskStore()
    expect(store.tasks).toEqual([])
    expect(store.loading).toBe(false)
    expect(store.filters.search).toBe('')
    expect(store.filters.status).toBe('')
    expect(store.filters.page).toBe(1)
  })

  it('updates filters and triggers fetchTasks', async () => {
    const listMock = vi.fn().mockResolvedValue({
      data: [{ id: 1, title: 'Task 1' }],
      meta: { total: 1, per_page: 10, current_page: 1 },
    })

    globalThis.useTasksApi = vi.fn(() => ({
      list: listMock,
      create: vi.fn(),
      update: vi.fn(),
      destroy: vi.fn(),
    }))

    const store = useTaskStore()
    
    await store.setFilters({ search: 'clean code', status: 'completed' })

    expect(store.filters.search).toBe('clean code')
    expect(store.filters.status).toBe('completed')
    expect(store.filters.page).toBe(1) // Resets page to 1
    expect(listMock).toHaveBeenCalled()
    expect(store.tasks).toEqual([{ id: 1, title: 'Task 1' }])
  })

  it('sets sorting order and toggles directions', async () => {
    const listMock = vi.fn().mockResolvedValue({
      data: [],
      meta: null,
    })

    globalThis.useTasksApi = vi.fn(() => ({
      list: listMock,
      create: vi.fn(),
      update: vi.fn(),
      destroy: vi.fn(),
    }))

    const store = useTaskStore()
    
    // Sort by due_date
    await store.setSort('due_date')
    expect(store.filters.sort_by).toBe('due_date')
    expect(store.filters.sort_direction).toBe('desc')

    // Click again to toggle direction
    await store.setSort('due_date')
    expect(store.filters.sort_by).toBe('due_date')
    expect(store.filters.sort_direction).toBe('asc')
  })
})
