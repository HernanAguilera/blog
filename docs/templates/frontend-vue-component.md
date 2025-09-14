# Frontend - Vue Component Template

```vue
<!-- Template: interface/components/{domain}/{ComponentName}.vue -->
<template>
  <div class="{component-name}">
    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center p-8">
      <LoadingSpinner class="w-8 h-8" />
    </div>

    <!-- Error State -->
    <ErrorMessage 
      v-else-if="error" 
      :message="error.message"
      @retry="handleRetry"
    />

    <!-- Content -->
    <div v-else class="space-y-4">
      <!-- ... component content ... -->
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { container } from '~/shared/container/container';
import type { {UseCase} } from '~/application/use-cases/{domain}/{UseCase}';
import type { {EntityName} } from '~/domain/entities/{EntityName}';

// ... imports and setup ...
</script>

<style scoped>
.{component-name} {
  /* Component specific styles */
}
</style>
```