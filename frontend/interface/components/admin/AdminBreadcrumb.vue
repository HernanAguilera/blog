<template>
  <nav class="mb-6">
    <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
      <!-- Home icon -->
      <li>
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
      </li>

      <!-- Dynamic breadcrumb items -->
      <li v-for="(item, index) in items" :key="index">
        <span class="text-gray-400 dark:text-gray-500">›</span>
        <NuxtLink
          v-if="item.to && index < items.length - 1"
          :to="item.to"
          class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
        >
          {{ item.label }}
        </NuxtLink>
        <span
          v-else
          class="text-gray-900 dark:text-white font-medium"
        >
          {{ item.label }}
        </span>
      </li>
    </ol>
  </nav>
</template>

<script setup lang="ts">
export interface BreadcrumbItem {
  label: string;
  to?: string;
}

interface Props {
  items: BreadcrumbItem[];
}

defineProps<Props>();
</script>