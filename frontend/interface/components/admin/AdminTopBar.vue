<template>
  <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-between items-center">
    <div>
      <!-- Breadcrumb navigation will be handled by individual pages -->
    </div>

    <!-- User menu -->
    <div class="flex items-center space-x-4">
      <div class="relative">
        <button
          @click="showUserMenu = !showUserMenu"
          class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
        >
          <span class="text-sm text-gray-500 dark:text-gray-400" v-if="user">{{ user.getEmail() }}</span>
          <span class="text-sm font-medium" v-if="user">Mi Perfil</span>
          <span class="text-xs text-gray-400 dark:text-gray-500" v-if="user">{{ user.getRole().value() }}</span>
          <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
            <svg class="w-4 h-4 text-gray-700 dark:text-gray-200" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
            </svg>
          </div>
        </button>

        <!-- Dropdown menu -->
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
              {{ isDarkMode ? 'Tema claro' : 'Tema oscuro' }}
            </button>
            <hr class="my-1">
            <button
              @click="handleLogout"
              class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
            >
              Cerrar sesión
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useAuthStore } from '~/interface/stores/auth.store';
import { useRouter } from 'vue-router';

// Composables
const authStore = useAuthStore();
const router = useRouter();

// State
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

// Close menu when route changes
router.afterEach(() => {
  showUserMenu.value = false;
});
</script>