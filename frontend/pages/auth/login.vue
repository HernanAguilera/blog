<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div>
        <div class="mx-auto h-12 w-auto flex justify-center">
          <NuxtLink to="/" class="flex items-center space-x-2">
            <!-- Logo placeholder - replace with actual logo -->
            <div class="h-10 w-10 bg-blue-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-xl">B</span>
            </div>
            <span class="text-2xl font-bold text-gray-900 dark:text-white">BlogV2</span>
          </NuxtLink>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
          Inicia sesión en tu cuenta
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
          O
          <NuxtLink
            to="/auth/register"
            class="font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300"
          >
            crea una nueva cuenta
          </NuxtLink>
        </p>
      </div>

      <!-- Login Form -->
      <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow dark:shadow-gray-700 sm:rounded-lg sm:px-10">
        <LoginForm
          :redirect-to="redirectTo"
          @success="handleLoginSuccess"
          @error="handleLoginError"
          @social-login="handleSocialLogin"
        />
      </div>

      <!-- Success Message -->
      <div
        v-if="successMessage"
        class="rounded-md bg-green-50 dark:bg-green-900/50 p-4">
      >
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-green-800 dark:text-green-200">
              {{ successMessage }}
            </p>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div
        v-if="errorMessage"
        class="rounded-md bg-red-50 dark:bg-red-900/50 p-4">
      >
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
              {{ errorMessage }}
            </h3>
          </div>
        </div>
      </div>

      <!-- Footer Links -->
      <div class="text-center space-y-2">
        <div>
          <NuxtLink
            to="/auth/forgot-password"
            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300"
          >
            ¿Olvidaste tu contraseña?
          </NuxtLink>
        </div>
        <div>
          <NuxtLink
            to="/"
            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
          >
            ← Volver al inicio
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '~/interface/stores/auth.store';
import LoginForm from '~/interface/components/auth/LoginForm.vue';
import type { LoginPayload } from '~/interface/types/auth-store.types';

// Meta data
definePageMeta({
  layout: 'auth',
  middleware: 'guest'
});

// Head configuration
useHead({
  title: 'Iniciar Sesión | BlogV2',
  meta: [
    {
      name: 'description',
      content: 'Inicia sesión en tu cuenta de BlogV2 para acceder a tu dashboard y gestionar tu contenido.'
    },
    {
      name: 'robots',
      content: 'noindex, nofollow'
    }
  ]
});

// Composables
const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

// State
const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

// Computed
const redirectTo = computed(() => {
  const redirect = route.query.redirect as string;
  return redirect || '/dashboard';
});

// Methods
const handleLoginSuccess = async (payload: LoginPayload) => {
  successMessage.value = '¡Bienvenido de nuevo! Redirigiendo...';
  errorMessage.value = null;

  // Wait a moment to show success message
  await new Promise(resolve => setTimeout(resolve, 1000));

  // Redirect to intended destination
  await router.push(redirectTo.value);
};

const handleLoginError = (error: string) => {
  errorMessage.value = error;
  successMessage.value = null;

  // Clear error after 10 seconds
  setTimeout(() => {
    errorMessage.value = null;
  }, 10000);
};

const handleSocialLogin = async (provider: string) => {
  try {
    // Clear previous messages
    errorMessage.value = null;
    successMessage.value = null;

    // Get the base URL from runtime config
    const config = useRuntimeConfig();
    const baseUrl = config.public.apiBaseUrl;

    // Construct social login URL
    const socialUrl = `${baseUrl}/auth/social/${provider}`;
    const returnUrl = encodeURIComponent(window.location.origin + redirectTo.value);
    const fullUrl = `${socialUrl}?return_url=${returnUrl}`;

    // Redirect to social login
    window.location.href = fullUrl;
  } catch (error) {
    handleLoginError(`Error al iniciar sesión con ${provider}`);
  }
};

const clearMessages = () => {
  successMessage.value = null;
  errorMessage.value = null;
};

// Lifecycle
onMounted(async () => {
  // Check if user is already authenticated
  if (authStore.isAuthenticated) {
    await router.push(redirectTo.value);
    return;
  }

  // Try to restore session
  const restored = await authStore.restoreSession();
  if (restored) {
    await router.push(redirectTo.value);
    return;
  }

  // Check for success/error messages from query params
  const success = route.query.success as string;
  const error = route.query.error as string;

  if (success) {
    successMessage.value = decodeURIComponent(success);
    // Clear query params
    await router.replace({ query: {} });
  }

  if (error) {
    errorMessage.value = decodeURIComponent(error);
    // Clear query params
    await router.replace({ query: {} });
  }
});

// Watch for authentication changes
watch(() => authStore.isAuthenticated, async (newValue) => {
  if (newValue) {
    await router.push(redirectTo.value);
  }
});
</script>