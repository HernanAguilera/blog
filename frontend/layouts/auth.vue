<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Optional header for auth pages -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <!-- Logo -->
          <NuxtLink to="/" class="flex items-center space-x-2">
            <div class="h-8 w-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-sm">B</span>
            </div>
            <span class="text-xl font-bold text-gray-900">BlogV2</span>
          </NuxtLink>

          <!-- Navigation -->
          <nav class="hidden sm:flex space-x-6">
            <NuxtLink
              to="/"
              class="text-gray-600 hover:text-gray-900 text-sm font-medium"
            >
              Inicio
            </NuxtLink>
            <NuxtLink
              to="/about"
              class="text-gray-600 hover:text-gray-900 text-sm font-medium"
            >
              Acerca de
            </NuxtLink>
            <NuxtLink
              to="/contact"
              class="text-gray-600 hover:text-gray-900 text-sm font-medium"
            >
              Contacto
            </NuxtLink>
          </nav>

          <!-- Mobile menu button -->
          <div class="sm:hidden">
            <button
              type="button"
              class="text-gray-600 hover:text-gray-900"
              @click="mobileMenuOpen = !mobileMenuOpen"
            >
              <span class="sr-only">Abrir menú</span>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile menu -->
        <div v-show="mobileMenuOpen" class="sm:hidden border-t border-gray-200 py-4">
          <nav class="space-y-2">
            <NuxtLink
              to="/"
              class="block text-gray-600 hover:text-gray-900 text-base font-medium"
              @click="mobileMenuOpen = false"
            >
              Inicio
            </NuxtLink>
            <NuxtLink
              to="/about"
              class="block text-gray-600 hover:text-gray-900 text-base font-medium"
              @click="mobileMenuOpen = false"
            >
              Acerca de
            </NuxtLink>
            <NuxtLink
              to="/contact"
              class="block text-gray-600 hover:text-gray-900 text-base font-medium"
              @click="mobileMenuOpen = false"
            >
              Contacto
            </NuxtLink>
          </nav>
        </div>
      </div>
    </header>

    <!-- Main content -->
    <main>
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200">
      <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="text-center text-sm text-gray-500 space-y-2">
          <p>&copy; {{ currentYear }} BlogV2. Todos los derechos reservados.</p>
          <div class="space-x-4">
            <NuxtLink to="/privacy" class="hover:text-gray-700">
              Privacidad
            </NuxtLink>
            <NuxtLink to="/terms" class="hover:text-gray-700">
              Términos
            </NuxtLink>
            <NuxtLink to="/support" class="hover:text-gray-700">
              Soporte
            </NuxtLink>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

// State
const mobileMenuOpen = ref(false);

// Computed
const currentYear = computed(() => new Date().getFullYear());

// Close mobile menu when clicking outside
const closeMobileMenu = () => {
  mobileMenuOpen.value = false;
};

// Handle route changes to close mobile menu
const router = useRouter();
router.afterEach(() => {
  closeMobileMenu();
});
</script>