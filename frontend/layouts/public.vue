<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">
    <!-- Header -->
    <PublicHeader />

    <!-- Main Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <PublicFooter />
  </div>
</template>

<script setup lang="ts">
// SEO básico del layout
useHead({
  htmlAttrs: {
    lang: 'es'
  },
  meta: [
    { charset: 'utf-8' },
    { name: 'viewport', content: 'width=device-width, initial-scale=1' },
    { name: 'author', content: 'BlogV2' },
    { name: 'generator', content: 'Nuxt 3' }
  ]
})
</script>