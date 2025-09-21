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
  </div>
</template>

<script setup lang="ts">
// No need for any logic here - components handle their own state
</script>