import { describe, it, expect, beforeEach, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useAuthStore } from './auth'

describe('Auth Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  it('initializes with unauthenticated user', () => {
    const store = useAuthStore()
    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })

  it('logins successfully with correct credentials', async () => {
    const mockUser = { id: 1, name: 'Admin', email: 'admin@test.com', roles: ['admin'] }
    const mockToken = 'mock-bearer-token'

    const loginMock = vi.fn().mockResolvedValue({
      token: mockToken,
      user: mockUser,
    })

    // Mock useAuthApi globally
    globalThis.useAuthApi = vi.fn(() => ({
      login: loginMock,
      logout: vi.fn(),
      getUser: vi.fn(),
    }))

    const store = useAuthStore()
    await store.login({ email: 'admin@test.com', password: 'password' })

    expect(loginMock).toHaveBeenCalledWith({ email: 'admin@test.com', password: 'password' })
    expect(store.token).toBe(mockToken)
    expect(store.user).toEqual(mockUser)
    expect(store.isAuthenticated).toBe(true)
  })

  it('logouts and clears user and token', async () => {
    const logoutMock = vi.fn().mockResolvedValue({ message: 'Success' })

    globalThis.useAuthApi = vi.fn(() => ({
      login: vi.fn(),
      logout: logoutMock,
      getUser: vi.fn(),
    }))

    const store = useAuthStore()
    store.token = 'existing-token'
    store.user = { id: 1, name: 'Admin', email: 'admin@test.com', roles: ['admin'] }

    await store.logout()

    expect(logoutMock).toHaveBeenCalled()
    expect(store.token).toBeNull()
    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
    expect(globalThis.navigateTo).toHaveBeenCalledWith('/login')
  })
})
