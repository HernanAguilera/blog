<template>
  <aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 fixed h-full overflow-y-auto">
    <!-- Header -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
      <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Nombre de empresa</h1>
    </div>

    <!-- Navigation -->
    <nav class="mt-6">
      <div class="px-4 space-y-1">
        <NuxtLink
          to="/admin/posts"
          class="group flex items-center px-2 py-2 text-base font-medium text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md"
          :class="{ 'bg-gray-100 dark:bg-gray-700': $route.path.startsWith('/admin/posts') }"
        >
          Posts
        </NuxtLink>
        <NuxtLink
          to="/admin/pages"
          class="group flex items-center px-2 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded-md"
          :class="{ 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white': $route.path.startsWith('/admin/pages') }"
        >
          Páginas
        </NuxtLink>
        <NuxtLink
          to="/admin/comments"
          class="group flex items-center px-2 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded-md"
          :class="{ 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white': $route.path.startsWith('/admin/comments') }"
        >
          Comentarios
        </NuxtLink>
        <NuxtLink
          to="/admin/users"
          class="group flex items-center px-2 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded-md"
          :class="{ 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white': $route.path.startsWith('/admin/users') }"
        >
          Usuarios
        </NuxtLink>
      </div>

      <!-- Configuration section -->
      <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
        <div class="px-4">
          <NuxtLink
            to="/admin/settings"
            class="group flex items-center px-2 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white rounded-md"
            :class="{ 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white': $route.path === '/admin/settings' }"
          >
            Configuración
          </NuxtLink>
        </div>
      </div>
    </nav>
  </aside>
</template>

<script setup lang="ts">
// This component doesn't need any reactive data for now
// The route-based active states are handled via :class bindings
</script>