import { defineStore } from 'pinia'
import type { LoginCredentials, User } from '~/types'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = useCookie('auth_token', { maxAge: 60 * 60 * 24 * 7 })
  const authApi = useAuthApi()

  const isAuthenticated = computed(() => !!token.value)

  async function login(credentials: LoginCredentials): Promise<void> {
    const response = await authApi.login(credentials)
    token.value = response.token
    user.value = response.user
  }

  async function logout(): Promise<void> {
    try {
      await authApi.logout()
    }
    finally {
      token.value = null
      user.value = null
      navigateTo('/login')
    }
  }

  async function fetchUser(): Promise<void> {
    if (!token.value) return

    try {
      const response = await authApi.getUser()
      user.value = response.data
    }
    catch {
      token.value = null
      user.value = null
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    logout,
    fetchUser,
  }
})
