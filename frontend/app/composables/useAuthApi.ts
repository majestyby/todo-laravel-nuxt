import type {ApiResponse, LoginCredentials, LoginResponse, User} from "~/types";

export function useAuthApi() {
  const { get, post } = useApiFetch()

  return {
    login(credentials: LoginCredentials) {
      return post<LoginResponse>('/auth/login', credentials)
    },

    logout() {
      return post<{ message: string }>('/auth/logout')
    },

    getUser() {
      return get<ApiResponse<User>>('/user')
    },
  }
}
