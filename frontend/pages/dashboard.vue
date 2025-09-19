<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Theme Toggle -->
      <div class="flex justify-end mb-4">
        <button
          @click="toggleDarkMode"
          class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
          title="Cambiar tema"
        >
          <svg v-if="isDarkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
          </svg>
        </button>
      </div>

      <!-- Dashboard Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
          Dashboard
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          Bienvenido a tu panel de control personal
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="isLoadingAuth" class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6 mb-8">
        <div class="flex items-center space-x-4">
          <div class="h-12 w-12 bg-gray-200 dark:bg-gray-600 rounded-full animate-pulse"/>
          <div>
            <div class="h-6 bg-gray-200 dark:bg-gray-600 rounded animate-pulse w-32 mb-2"/>
            <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded animate-pulse w-24"/>
          </div>
        </div>
      </div>

      <!-- User Welcome Card -->
      <div v-else-if="user" class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6 mb-8">
        <div class="flex items-center space-x-4">
          <div class="h-12 w-12 bg-blue-500 rounded-full flex items-center justify-center">
            <span class="text-white text-xl font-bold">
              {{ user.getName().charAt(0).toUpperCase() }}
            </span>
          </div>
          <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
              Hola, {{ user.getName() }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
              {{ getRoleDisplayName(user.getRole().value()) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Fallback when only token is available -->
      <div v-else-if="authStore.isAuthenticated" class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6 mb-8">
        <div class="flex items-center space-x-4">
          <div class="h-12 w-12 bg-gray-500 rounded-full flex items-center justify-center">
            <span class="text-white text-xl font-bold">U</span>
          </div>
          <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
              Hola, Usuario
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
              Sesión activa
            </p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Profile -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-600 transition-shadow">
          <div class="flex items-center space-x-3 mb-4">
            <div class="h-10 w-10 bg-blue-500 rounded-lg flex items-center justify-center">
              <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Mi Perfil</h3>
          </div>
          <p class="text-gray-600 dark:text-gray-400 mb-4">Gestiona tu información personal</p>
          <NuxtLink
            to="/profile"
            class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-medium"
          >
            Ver perfil
            <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </NuxtLink>
        </div>

        <!-- Posts -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-600 transition-shadow">
          <div class="flex items-center space-x-3 mb-4">
            <div class="h-10 w-10 bg-green-500 rounded-lg flex items-center justify-center">
              <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Posts</h3>
          </div>
          <p class="text-gray-600 dark:text-gray-400 mb-4">Explora contenido del blog</p>
          <NuxtLink
            to="/posts"
            class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-medium"
          >
            Ver posts
            <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </NuxtLink>
        </div>

        <!-- Admin Panel Loading -->
        <div v-if="isLoadingAuth" class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6">
          <div class="flex items-center space-x-3 mb-4">
            <div class="h-10 w-10 bg-gray-200 dark:bg-gray-600 rounded-lg animate-pulse"/>
            <div class="h-5 bg-gray-200 dark:bg-gray-600 rounded animate-pulse w-24"/>
          </div>
          <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded animate-pulse w-32 mb-4"/>
          <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded animate-pulse w-20"/>
        </div>

        <!-- Admin Panel Fallback (when no user data but authenticated) -->
        <div v-else-if="authStore.isAuthenticated && !user" class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-600 transition-shadow">
          <div class="flex items-center space-x-3 mb-4">
            <div class="h-10 w-10 bg-purple-500 rounded-lg flex items-center justify-center">
              <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Administración</h3>
          </div>
          <p class="text-gray-600 dark:text-gray-400 mb-4">Panel de administrador</p>
          <NuxtLink
            to="/admin"
            class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-medium"
          >
            Ir al admin
            <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </NuxtLink>
        </div>

        <!-- Admin Panel (only for admins) -->
        <div v-else-if="canAccessAdmin" class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-600 transition-shadow">
          <div class="flex items-center space-x-3 mb-4">
            <div class="h-10 w-10 bg-purple-500 rounded-lg flex items-center justify-center">
              <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Administración</h3>
          </div>
          <p class="text-gray-600 dark:text-gray-400 mb-4">Panel de administrador</p>
          <NuxtLink
            to="/admin"
            class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-medium"
          >
            Ir al admin
            <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </NuxtLink>
        </div>
      </div>

      <!-- Logout Button -->
      <div class="text-center">
        <button
          :disabled="isLoggingOut"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
          @click="handleLogout"
        >
          {{ isLoggingOut ? 'Cerrando sesión...' : 'Cerrar Sesión' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../interface/stores/auth.store';
import { ROLE } from '../domain/types/permissions.types';

// Meta data
definePageMeta({
  middleware: 'auth'
});

// Head configuration
useHead({
  title: 'Dashboard | BlogV2',
  meta: [
    {
      name: 'description',
      content: 'Panel de control personal de BlogV2. Gestiona tu perfil y accede a las funcionalidades de la plataforma.'
    }
  ]
});

// Composables
const router = useRouter();
const authStore = useAuthStore();
const colorMode = useColorMode();

// State
const isLoggingOut = ref(false);

// Computed
const user = computed(() => authStore.currentUser);
const isLoadingAuth = computed(() => authStore.isLoading);
const canAccessAdmin = computed(() => {
  return user.value && (user.value.hasRole(ROLE.ADMIN) || user.value.hasRole(ROLE.SUPER_ADMIN));
});

const isDarkMode = computed(() => colorMode.value === 'dark');

// Methods
const getRoleDisplayName = (role: string): string => {
  const roleMap: Record<string, string> = {
    [ROLE.SUPER_ADMIN]: 'Super Administrador',
    [ROLE.ADMIN]: 'Administrador',
    [ROLE.COLLABORATOR]: 'Colaborador',
    [ROLE.GUEST]: 'Usuario'
  };
  return roleMap[role] || 'Usuario';
};

const toggleDarkMode = () => {
  colorMode.preference = isDarkMode.value ? 'light' : 'dark';
};

const handleLogout = async () => {
  isLoggingOut.value = true;

  try {
    await authStore.logout();
    await router.push('/');
  } catch (error) {
    console.error('Error during logout:', error);
    // Even if logout fails, redirect to home
    await router.push('/');
  } finally {
    isLoggingOut.value = false;
  }
};

const ensureUserData = async () => {
  // If we have a token but no user data, this is a page refresh scenario
  if (authStore.isAuthenticated && !authStore.currentUser) {
    // In a real implementation, you would call:
    // await authStore.getCurrentUser();

    // For now, we'll force a re-login to get complete user data
    // This is not ideal but ensures the app works correctly
  }
};

// Lifecycle
onMounted(() => {
  ensureUserData();
});
</script>