<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Admin Header -->
    <header class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <!-- Logo and Title -->
          <div class="flex items-center space-x-4">
            <NuxtLink to="/admin" class="flex items-center space-x-2">
              <div class="h-8 w-8 bg-purple-600 rounded-lg flex items-center justify-center">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <span class="text-xl font-bold text-gray-900 dark:text-white">Panel Admin</span>
            </NuxtLink>
          </div>

          <!-- Navigation -->
          <nav class="hidden md:flex space-x-6">
            <NuxtLink
              to="/admin/posts"
              class="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white text-sm font-medium"
              :class="{ 'text-purple-600 dark:text-purple-400': $route.path.startsWith('/admin/posts') }"
            >
              Posts
            </NuxtLink>
            <NuxtLink
              to="/admin/users"
              class="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white text-sm font-medium"
              :class="{ 'text-purple-600 dark:text-purple-400': $route.path.startsWith('/admin/users') }"
            >
              Usuarios
            </NuxtLink>
            <NuxtLink
              to="/admin/comments"
              class="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white text-sm font-medium"
              :class="{ 'text-purple-600 dark:text-purple-400': $route.path.startsWith('/admin/comments') }"
            >
              Comentarios
            </NuxtLink>
          </nav>

          <!-- User Actions -->
          <div class="flex items-center space-x-4">
            <!-- User Info -->
            <div v-if="user" class="hidden sm:flex items-center space-x-3">
              <div class="flex flex-col text-right">
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ user.getName() }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ user.getRole().value() }}</span>
              </div>
              <div class="h-8 w-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                  {{ user.getName().charAt(0).toUpperCase() }}
                </span>
              </div>
            </div>

            <!-- Actions Menu -->
            <div class="relative">
              <button
                class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
                @click="showUserMenu = !showUserMenu"
              >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                </svg>
              </button>

              <!-- User Menu Dropdown -->
              <div
                v-if="showUserMenu"
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-600 z-10"
                @click.away="showUserMenu = false"
              >
                <div class="py-1">
                  <NuxtLink
                    to="/profile"
                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="showUserMenu = false"
                  >
                    Mi Perfil
                  </NuxtLink>
                  <NuxtLink
                    to="/admin/posts"
                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="showUserMenu = false"
                  >
                    Mis Posts
                  </NuxtLink>
                  <NuxtLink
                    to="/"
                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="showUserMenu = false"
                  >
                    Ver sitio público
                  </NuxtLink>
                  <button
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center"
                    @click="toggleDarkMode"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        v-if="isDarkMode"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                      ></path>
                      <path
                        v-else
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                      ></path>
                    </svg>
                    {{ isDarkMode ? 'Modo claro' : 'Modo oscuro' }}
                  </button>
                  <hr class="my-1">
                  <button
                    class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
                    @click="handleLogout"
                  >
                    Cerrar sesión
                  </button>
                </div>
              </div>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
              <button
                class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                @click="mobileMenuOpen = !mobileMenuOpen"
              >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Mobile Navigation -->
        <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-200 dark:border-gray-600 py-4">
          <nav class="space-y-2">
            <NuxtLink
              to="/admin"
              class="block text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white text-base font-medium py-2"
              @click="mobileMenuOpen = false"
            >
              Dashboard
            </NuxtLink>
            <NuxtLink
              to="/admin/posts"
              class="block text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white text-base font-medium py-2"
              @click="mobileMenuOpen = false"
            >
              Posts
            </NuxtLink>
            <NuxtLink
              to="/admin/users"
              class="block text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white text-base font-medium py-2"
              @click="mobileMenuOpen = false"
            >
              Usuarios
            </NuxtLink>
            <NuxtLink
              to="/admin/comments"
              class="block text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white text-base font-medium py-2"
              @click="mobileMenuOpen = false"
            >
              Comentarios
            </NuxtLink>
          </nav>
        </div>
      </div>
    </header>

    <!-- Main content -->
    <main>
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useAuthStore } from '../interface/stores/auth.store';
import { useRouter } from 'vue-router';

// Composables
const authStore = useAuthStore();
const router = useRouter();

// State
const mobileMenuOpen = ref(false);
const showUserMenu = ref(false);

// Computed
const user = computed(() => authStore.currentUser);

// Dark mode
const colorMode = useColorMode();
const isDarkMode = computed(() => colorMode.value === 'dark');

// Methods
const toggleDarkMode = () => {
  colorMode.preference = isDarkMode.value ? 'light' : 'dark';
  showUserMenu.value = false;
};

const handleLogout = async () => {
  showUserMenu.value = false;
  try {
    await authStore.logout();
    await router.push('/auth/login');
  } catch (error) {
    console.error('Error logging out:', error);
  }
};

// Close menus when route changes
router.afterEach(() => {
  mobileMenuOpen.value = false;
  showUserMenu.value = false;
});
</script>