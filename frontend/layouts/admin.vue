<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex">
    <!-- Sidebar -->
    <AdminSidebar />

    <!-- Main content -->
    <div class="flex-1 ml-64">
      <!-- Top bar -->
      <AdminTopBar />

      <!-- Page content -->
      <main class="p-6">
        <slot />
      </main>
    </div>

    <!-- Admin-specific Loading Overlay -->
    <LoadingOverlay />
  </div>
</template>

<script setup lang="ts">
import LoadingOverlay from '~/interface/components/ui/LoadingOverlay.vue';

// Force client-side rendering for admin layout
definePageMeta({
  ssr: false
});
</script>