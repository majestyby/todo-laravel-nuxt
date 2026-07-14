<script setup lang="ts">
import type { LoginCredentials, ApiError } from '~/types'

definePageMeta({ middleware: 'auth' })

useHead({
  title: 'Login',
})

const authStore = useAuthStore()
const toast = useToast()
const loading = ref(false)

const form = reactive<LoginCredentials>({
  email: '',
  password: '',
})

const errors = ref<Record<string, string[]>>({})

async function handleLogin() {
  loading.value = true
  errors.value = {}

  try {
    await authStore.login(form)
    navigateTo('/tasks')
  }
  catch (error: any) {
    const data = error?.data as ApiError | undefined
    if (data?.errors) {
      errors.value = data.errors
    }
    else {
      toast.add({
        title: 'Login failed',
        description: data?.message || 'Invalid credentials.',
        color: 'error',
      })
    }
  }
  finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-[80vh] flex items-center justify-center">
    <div class="w-full max-w-md">
      <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-8">
          Sign in to Task Manager
        </h1>

        <form class="space-y-6" @submit.prevent="handleLogin">
          <UFormField label="Email" :error="errors.email?.[0]">
            <UInput
              v-model="form.email"
              type="email"
              placeholder="admin@test.com"
              size="lg"
              class="w-full"
              autocomplete="email"
            />
          </UFormField>

          <UFormField label="Password" :error="errors.password?.[0]">
            <UInput
              v-model="form.password"
              type="password"
              placeholder="••••••••"
              size="lg"
              class="w-full"
              autocomplete="current-password"
            />
          </UFormField>

          <UButton
            type="submit"
            block
            size="lg"
            :loading="loading"
          >
            Sign in
          </UButton>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
          Test credentials: admin@test.com / password
        </p>
      </div>
    </div>
  </div>
</template>
