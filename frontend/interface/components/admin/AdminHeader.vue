<template>
  <div class="flex justify-between items-center mb-6">
    <!-- Title and description -->
    <div>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ title }}</h1>
      <p v-if="description" class="text-gray-600 dark:text-gray-300">{{ description }}</p>
    </div>

    <!-- Action buttons -->
    <div v-if="$slots.actions" class="flex items-center space-x-3">
      <slot name="actions"></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  title: string;
  description?: string;
}

defineProps<Props>();
</script>