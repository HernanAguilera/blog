<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Welcome Header -->
      <div class="text-center mb-12">
        <div class="mx-auto h-16 w-16 bg-green-500 rounded-full flex items-center justify-center mb-6">
          <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>

        <h1 class="text-4xl font-bold text-gray-900 mb-4">
          {{ isNewUser ? '¡Bienvenido a BlogV2!' : '¡Bienvenido de vuelta!' }}
        </h1>

        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          {{ isNewUser
            ? 'Tu cuenta ha sido creada exitosamente. Estamos emocionados de tenerte como parte de nuestra comunidad.'
            : 'Nos alegra verte de nuevo. Tu sesión ha sido restaurada correctamente.'
          }}
        </p>
      </div>

      <!-- User Info Card -->
      <div v-if="user" class="bg-white rounded-lg shadow-lg p-8 mb-8">
        <div class="flex items-center space-x-4 mb-6">
          <div class="h-16 w-16 bg-blue-500 rounded-full flex items-center justify-center">
            <span class="text-white text-2xl font-bold">
              {{ user.getName().charAt(0).toUpperCase() }}
            </span>
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ user.getName() }}</h2>
            <p class="text-gray-600">{{ user.getEmail().value() }}</p>
            <span
class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getRoleBadgeClass(user.getRole().value())">
              {{ getRoleDisplayName(user.getRole().value()) }}
            </span>
          </div>
        </div>

        <!-- Account Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
              <svg
class="h-5 w-5" :class="user.getEmailVerified() ? 'text-green-500' : 'text-yellow-500'"
                   fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900">Estado del email</p>
              <p class="text-sm text-gray-600">
                {{ user.getEmailVerified() ? 'Verificado' : 'Pendiente de verificación' }}
              </p>
            </div>
          </div>

          <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
              <svg
class="h-5 w-5" :class="user.getIsActive() ? 'text-green-500' : 'text-red-500'"
                   fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900">Estado de la cuenta</p>
              <p class="text-sm text-gray-600">
                {{ user.getIsActive() ? 'Activa' : 'Inactiva' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Dashboard -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
          <div class="flex items-center space-x-3 mb-4">
            <div class="h-10 w-10 bg-blue-500 rounded-lg flex items-center justify-center">
              <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Dashboard</h3>
          </div>
          <p class="text-gray-600 mb-4">Accede a tu panel de control personal</p>
          <NuxtLink
            to="/profile"
            class="inline-flex items-center text-blue-600 hover:text-blue-500 font-medium"
          >
            Ir al perfil
            <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </NuxtLink>
        </div>

        <!-- Browse Posts -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
          <div class="flex items-center space-x-3 mb-4">
            <div class="h-10 w-10 bg-green-500 rounded-lg flex items-center justify-center">
              <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Explorar Posts</h3>
          </div>
          <p class="text-gray-600 mb-4">Descubre contenido interesante en nuestro blog</p>
          <NuxtLink
            to="/posts"
            class="inline-flex items-center text-blue-600 hover:text-blue-500 font-medium"
          >
            Ver posts
            <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </NuxtLink>
        </div>

        <!-- Profile Settings -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
          <div class="flex items-center space-x-3 mb-4">
            <div class="h-10 w-10 bg-purple-500 rounded-lg flex items-center justify-center">
              <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Perfil</h3>
          </div>
          <p class="text-gray-600 mb-4">Configura tu perfil y preferencias</p>
          <NuxtLink
            to="/profile"
            class="inline-flex items-center text-blue-600 hover:text-blue-500 font-medium"
          >
            Editar perfil
            <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </NuxtLink>
        </div>
      </div>

      <!-- Email Verification Notice -->
      <div v-if="user && !user.getEmailVerified()" class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-8">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">
              Verificación de email pendiente
            </h3>
            <div class="mt-2 text-sm text-yellow-700">
              <p>
                Te hemos enviado un email de verificación a <strong>{{ user.getEmail().value() }}</strong>.
                Por favor revisa tu bandeja de entrada y sigue las instrucciones para verificar tu cuenta.
              </p>
            </div>
            <div class="mt-4">
              <button
                type="button"
                class="bg-yellow-100 px-3 py-2 rounded-md text-sm font-medium text-yellow-800 hover:bg-yellow-200"
                :disabled="isResendingEmail"
                @click="resendVerificationEmail"
              >
                {{ isResendingEmail ? 'Enviando...' : 'Reenviar email de verificación' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Continue Button -->
      <div class="text-center">
        <NuxtLink
          to="/profile"
          class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Continuar al Perfil
          <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../interface/stores/auth.store';
import { ROLE } from '../domain/types/permissions.types';

// Meta data
definePageMeta({
  middleware: 'auth'
});

// Head configuration
useHead({
  title: 'Bienvenido | BlogV2',
  meta: [
    {
      name: 'description',
      content: 'Bienvenido a BlogV2. Tu cuenta está lista para usar.'
    }
  ]
});

// Composables
const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

// State
const isResendingEmail = ref(false);

// Computed
const user = computed(() => authStore.currentUser);
const isNewUser = computed(() => {
  return route.query.newUser === 'true';
});

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

const getRoleBadgeClass = (role: string): string => {
  const classMap: Record<string, string> = {
    [ROLE.SUPER_ADMIN]: 'bg-red-100 text-red-800',
    [ROLE.ADMIN]: 'bg-purple-100 text-purple-800',
    [ROLE.COLLABORATOR]: 'bg-blue-100 text-blue-800',
    [ROLE.GUEST]: 'bg-gray-100 text-gray-800'
  };
  return classMap[role] || 'bg-gray-100 text-gray-800';
};

const resendVerificationEmail = async () => {
  if (!user.value) return;

  isResendingEmail.value = true;

  try {
    // TODO: Implement resend verification email API call
    // await authService.resendVerificationEmail();

    // Simulate API call for now
    await new Promise(resolve => setTimeout(resolve, 2000));

    // Show success message
    const notification = useNotification();
    notification.success('Email de verificación enviado. Por favor revisa tu bandeja de entrada.');
  } catch (error) {
    // Show error message
    const notification = useNotification();
    notification.error('Error al enviar el email de verificación. Por favor intenta de nuevo.');
  } finally {
    isResendingEmail.value = false;
  }
};

// Lifecycle
onMounted(() => {
  // If user is not authenticated, redirect to login
  if (!authStore.isAuthenticated) {
    router.push('/auth/login');
  }
});
</script>