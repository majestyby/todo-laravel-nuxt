import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { ref, nextTick } from 'vue'
import TaskSearch from './TaskSearch.vue'

describe('TaskSearch Component', () => {
  let mockStore: any

  beforeEach(() => {
    vi.clearAllMocks()

    // Create a mock store state
    mockStore = {
      filters: reactive({
        search: '',
      }),
      setFilters: vi.fn(),
    }

    // Mock useTaskStore to return our mockStore
    globalThis.useTaskStore = vi.fn(() => mockStore)
  })

  it('initializes search input with value from store', () => {
    mockStore.filters.search = 'existing search'
    
    const wrapper = mount(TaskSearch, {
      global: {
        stubs: {
          UInput: true,
          UButton: true,
        },
      },
    })

    // The script setup defines `search` ref which is bound to UInput
    // We can access the component's exposed variables
    expect(wrapper.vm.search).toBe('existing search')
  })

  it('triggers setFilters when search input changes', async () => {
    const wrapper = mount(TaskSearch, {
      global: {
        stubs: {
          UInput: true,
          UButton: true,
        },
      },
    })

    // Change the search value
    wrapper.vm.search = 'vue 3'
    await nextTick()

    expect(mockStore.setFilters).toHaveBeenCalledWith({ search: 'vue 3' })
  })

  it('synchronizes internal search value when store filters change externally', async () => {
    const wrapper = mount(TaskSearch, {
      global: {
        stubs: {
          UInput: true,
          UButton: true,
        },
      },
    })

    expect(wrapper.vm.search).toBe('')

    // Update store search value externally
    mockStore.filters.search = 'nuxt 3'
    await nextTick()
    await nextTick() // double tick for watcher execution

    expect(wrapper.vm.search).toBe('nuxt 3')
  })
})
