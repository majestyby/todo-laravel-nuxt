<script setup lang="ts">
const authStore = useAuthStore()

useHead({
  titleTemplate: '%s - Task Manager',
  title: 'Task Manager',
})

onMounted(async () => {
  if (authStore.isAuthenticated) {
    await authStore.fetchUser()
  }
})
</script>

<template>
  <UApp>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </UApp>
</template>
