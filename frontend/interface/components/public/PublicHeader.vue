<template>
  <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex-shrink-0">
          <NuxtLink
            to="/"
            class="text-2xl font-bold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
          >
            BlogV2
          </NuxtLink>
        </div>

        <!-- Desktop Navigation -->
        <PublicNavigation class="hidden md:block" />

        <!-- Mobile menu button -->
        <div class="md:hidden">
          <button
            @click="toggleMobileMenu"
            type="button"
            class="bg-gray-200 dark:bg-gray-700 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
            aria-controls="mobile-menu"
            :aria-expanded="mobileMenuOpen"
          >
            <span class="sr-only">{{ $t('nav.openMenu') }}</span>
            <!-- Hamburger icon -->
            <svg
              class="h-6 w-6"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              :class="{ 'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <!-- Close icon -->
            <svg
              class="h-6 w-6"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              :class="{ 'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div
      class="md:hidden"
      :class="{ 'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }"
      id="mobile-menu"
    >
      <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <PublicNavigation :mobile="true" @navigate="closeMobileMenu" />
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
const mobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

const closeMobileMenu = () => {
  mobileMenuOpen.value = false
}

// Cerrar menú móvil cuando se cambia de ruta
const route = useRoute()
watch(() => route.path, () => {
  closeMobileMenu()
})
</script>