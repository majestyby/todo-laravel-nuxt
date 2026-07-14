<script setup lang="ts">
const authStore = useAuthStore()
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
    <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <NuxtLink to="/tasks" class="text-xl font-bold text-gray-900 dark:text-white">
            📋 Task Manager
          </NuxtLink>

          <div v-if="authStore.isAuthenticated" class="flex items-center gap-4">
            <span class="text-sm text-gray-600 dark:text-gray-400">
              {{ authStore.user?.name }}
            </span>
            <UButton
              color="neutral"
              variant="ghost"
              size="sm"
              @click="authStore.logout()"
            >
              Logout
            </UButton>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>
  </div>
</template>
