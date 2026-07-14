<template>
  <div class="space-y-4">
    <div v-for="i in 5" :key="i" class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-5 animate-pulse">
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1">
          <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-2/3" />
          <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-full mt-2" />
        </div>
        <div class="flex gap-1">
          <div class="h-7 w-7 bg-gray-200 dark:bg-gray-700 rounded" />
          <div class="h-7 w-7 bg-gray-200 dark:bg-gray-700 rounded" />
        </div>
      </div>
      <div class="mt-4 flex items-center gap-3">
        <div class="h-5 w-20 bg-gray-200 dark:bg-gray-700 rounded-full" />
        <div class="h-4 w-24 bg-gray-100 dark:bg-gray-800 rounded" />
      </div>
    </div>
  </div>
</template>
