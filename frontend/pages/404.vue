<template>
  <NuxtLayout name="public">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
      <div class="max-w-lg w-full text-center">
        <!-- 404 Illustration -->
        <div class="mb-8">
          <div class="relative">
            <!-- Large 404 Text -->
            <h1 class="text-9xl font-bold text-gray-200 dark:text-gray-700 select-none">
              404
            </h1>

            <!-- Floating elements for visual interest -->
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="w-24 h-24 bg-blue-100 dark:bg-blue-900 rounded-full opacity-20 animate-pulse"/>
            </div>

            <!-- Search icon overlay -->
            <div class="absolute inset-0 flex items-center justify-center">
              <svg
                class="w-16 h-16 text-blue-600 dark:text-blue-400 animate-bounce"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
            </div>
          </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            ¡Oops! Página no encontrada
          </h2>
          <p class="text-lg text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
            La página que buscas no existe, ha sido movida o la URL es incorrecta.
          </p>
          <p class="text-sm text-gray-500 dark:text-gray-500">
            No te preocupes, esto pasa hasta en las mejores páginas web.
          </p>
        </div>

        <!-- Suggestions -->
        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            ¿Qué puedes hacer?
          </h3>
          <ul class="text-left space-y-2 text-gray-600 dark:text-gray-400">
            <li class="flex items-start">
              <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
              Verifica que la URL esté escrita correctamente
            </li>
            <li class="flex items-start">
              <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
              Navega desde la página de inicio
            </li>
            <li class="flex items-start">
              <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
              Usa el buscador para encontrar lo que necesitas
            </li>
          </ul>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
          <NuxtLink
            to="/"
            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Ir al inicio
          </NuxtLink>

          <button
            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            @click="goBack"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver atrás
          </button>
        </div>

        <!-- Search Section -->
        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
          <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            ¿Buscas algo específico?
          </h4>
          <div class="max-w-md mx-auto">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar artículos..."
                class="w-full px-4 py-3 pl-10 pr-12 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @keyup.enter="handleSearch"
              >
              <svg
                class="absolute left-3 top-3.5 h-5 w-5 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <button
                class="absolute right-2 top-2 p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400"
                @click="handleSearch"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 12h12"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
// SEO composable
const { setErrorSEO } = useSEO()

// State
const searchQuery = ref('')

// Configurar SEO para página 404
watchEffect(() => {
  setErrorSEO(404)
})

// Methods
const goBack = () => {
  if (import.meta.client) {
    // Si hay historial, volver atrás
    if (window.history.length > 1) {
      window.history.back()
    } else {
      // Si no hay historial, ir al inicio
      navigateTo('/')
    }
  }
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    navigateTo(`/?search=${encodeURIComponent(searchQuery.value.trim())}`)
  }
}
</script>