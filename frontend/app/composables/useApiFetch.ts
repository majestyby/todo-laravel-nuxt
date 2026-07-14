import type { ApiError } from '~/types'

export function useApiFetch() {
  const config = useRuntimeConfig()
  const token = useCookie('auth_token')

  async function apiFetch<T>(
    url: string,
    options: RequestInit = {},
  ): Promise<T> {
    const headers: Record<string, string> = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...(options.headers as Record<string, string> || {}),
    }

    if (token.value) {
      headers['Authorization'] = `Bearer ${token.value}`
    }

    const response = await $fetch.raw<T>(url, {
      baseURL: config.public.apiBase as string,
      headers,
      ...options,
      method: options.method as any,
    })

    return response._data as T
  }

  async function get<T>(url: string, params?: Record<string, any>): Promise<T> {
    const query = params
      ? '?' + new URLSearchParams(
        Object.entries(params)
          .filter(([, v]) => v !== undefined && v !== null && v !== '')
          .map(([k, v]) => [k, String(v)]),
      ).toString()
      : ''

    return apiFetch<T>(`${url}${query}`, { method: 'GET' })
  }

  async function post<T>(url: string, body?: unknown): Promise<T> {
    return apiFetch<T>(url, {
      method: 'POST',
      body: body ? JSON.stringify(body) : undefined,
    })
  }

  async function patch<T>(url: string, body?: unknown): Promise<T> {
    return apiFetch<T>(url, {
      method: 'PATCH',
      body: body ? JSON.stringify(body) : undefined,
    })
  }

  async function del<T>(url: string): Promise<T> {
    return apiFetch<T>(url, { method: 'DELETE' })
  }

  return { get, post, patch, del }
}
