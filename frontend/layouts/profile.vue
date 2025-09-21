<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-600 fixed h-full overflow-y-auto">
      <!-- Header -->
      <div class="p-4 border-b border-gray-200 dark:border-gray-600">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Nombre de empresa</h1>
      </div>

      <!-- Navigation -->
      <nav class="mt-6">
        <div class="px-4 space-y-1">
          <NuxtLink
            to="/profile"
            class="group flex items-center px-2 py-2 text-base font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md"
            :class="{ 'bg-gray-100 dark:bg-gray-700': $route.path === '/profile' }"
          >
            Mi perfil
          </NuxtLink>
          <NuxtLink
            to="/profile/posts"
            class="group flex items-center px-2 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 rounded-md"
            :class="{ 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100': $route.path.startsWith('/profile/posts') }"
          >
            Mis posts
          </NuxtLink>
          <NuxtLink
            to="/profile/comments"
            class="group flex items-center px-2 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 rounded-md"
            :class="{ 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100': $route.path.startsWith('/profile/comments') }"
          >
            Mis comentarios
          </NuxtLink>
          <NuxtLink
            to="/profile/change-password"
            class="group flex items-center px-2 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 rounded-md"
            :class="{ 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100': $route.path === '/profile/change-password' }"
          >
            Cambiar contraseña
          </NuxtLink>
          <button
            @click="handleDeleteAccount"
            class="w-full text-left group flex items-center px-2 py-2 text-base font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700 dark:hover:text-red-300 rounded-md"
          >
            Eliminar cuenta
          </button>
        </div>

        <!-- Configuration section -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
          <div class="px-4">
            <NuxtLink
              to="/profile/settings"
              class="group flex items-center px-2 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 rounded-md"
              :class="{ 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100': $route.path === '/profile/settings' }"
            >
              Configuración
            </NuxtLink>
          </div>
        </div>
      </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1 ml-64">
      <!-- Top bar -->
      <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600 px-6 py-4 flex justify-between items-center">
        <div>
          <!-- Breadcrumb navigation will be handled by individual pages -->
        </div>

        <!-- User menu -->
        <div class="flex items-center space-x-4">
          <div class="relative">
            <button
              @click="showUserMenu = !showUserMenu"
              class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100"
            >
              <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
              </div>
              <span class="text-sm font-medium" v-if="user">{{ user.getName() }}</span>
            </button>

            <!-- Dropdown menu -->
            <div
              v-if="showUserMenu"
              class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-600 z-10"
              @click.away="showUserMenu = false"
            >
              <div class="py-1">
                <NuxtLink
                  to="/"
                  class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                  @click="showUserMenu = false"
                >
                  Ver sitio público
                </NuxtLink>
                <NuxtLink
                  v-if="canAccessAdmin"
                  to="/admin"
                  class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                  @click="showUserMenu = false"
                >
                  Panel Admin
                </NuxtLink>
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

      <!-- Page content -->
      <main class="p-6">
        <slot />
      </main>
    </div>
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
const showUserMenu = ref(false);

// Computed
const user = computed(() => authStore.currentUser);
const canAccessAdmin = computed(() => {
  if (!user.value) return false;
  const role = user.value.getRole().value();
  return ['SuperAdmin', 'Admin'].includes(role);
});

// Methods
const handleLogout = async () => {
  showUserMenu.value = false;
  try {
    await authStore.logout();
    await router.push('/auth/login');
  } catch (error) {
    console.error('Error logging out:', error);
  }
};

const handleDeleteAccount = () => {
  // TODO: Implement delete account functionality
  console.log('Delete account clicked');
};

// Close menu when route changes
router.afterEach(() => {
  showUserMenu.value = false;
});
</script>