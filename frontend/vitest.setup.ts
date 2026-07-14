import { ref, computed, reactive, watch, onMounted, nextTick } from 'vue'
import { vi } from 'vitest'

// Expose Vue auto-imports globally for Nuxt 4 environment simulation
globalThis.ref = ref
globalThis.computed = computed
globalThis.reactive = reactive
globalThis.watch = watch
globalThis.onMounted = onMounted
globalThis.nextTick = nextTick

// Mock Nuxt-specific global functions
globalThis.useCookie = vi.fn((name, options) => {
  const value = ref(null)
  return value
})

globalThis.useAuthApi = vi.fn(() => ({
  login: vi.fn(),
  logout: vi.fn(),
  getUser: vi.fn(),
}))

globalThis.useTasksApi = vi.fn(() => ({
  list: vi.fn(),
  create: vi.fn(),
  update: vi.fn(),
  destroy: vi.fn(),
}))

globalThis.useToast = vi.fn(() => ({
  add: vi.fn(),
}))

globalThis.navigateTo = vi.fn()
globalThis.useDebounceFn = vi.fn((fn) => fn)
globalThis.useTaskStore = vi.fn()
globalThis.useRoute = vi.fn(() => ({
  query: {},
}))
globalThis.useRouter = vi.fn(() => ({
  replace: vi.fn(),
}))
globalThis.useHead = vi.fn()

// Stub for UInput, UButton, etc. to avoid registration issues during mounting
vi.mock('#app', () => ({
  useCookie: globalThis.useCookie,
  navigateTo: globalThis.navigateTo,
}))
