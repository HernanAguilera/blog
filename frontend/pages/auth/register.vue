<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div>
        <div class="mx-auto h-12 w-auto flex justify-center">
          <NuxtLink to="/" class="flex items-center space-x-2">
            <!-- Logo placeholder - replace with actual logo -->
            <div class="h-10 w-10 bg-blue-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-xl">B</span>
            </div>
            <span class="text-2xl font-bold text-gray-900">BlogV2</span>
          </NuxtLink>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Crea tu cuenta
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          O
          <NuxtLink
            to="/auth/login"
            class="font-medium text-blue-600 hover:text-blue-500"
          >
            inicia sesión si ya tienes una cuenta
          </NuxtLink>
        </p>
      </div>

      <!-- Registration Form -->
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <RegisterForm
          :redirect-to="redirectTo"
          @success="handleRegisterSuccess"
          @error="handleRegisterError"
        />
      </div>

      <!-- Success Message -->
      <div
        v-if="successMessage"
        class="rounded-md bg-green-50 p-4"
      >
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-green-800">
              {{ successMessage }}
            </p>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div
        v-if="errorMessage"
        class="rounded-md bg-red-50 p-4"
      >
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">
              {{ errorMessage }}
            </h3>
          </div>
        </div>
      </div>

      <!-- Information Box -->
      <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">
              Beneficios de crear una cuenta
            </h3>
            <div class="mt-2 text-sm text-blue-700">
              <ul class="list-disc list-inside space-y-1">
                <li>Acceso completo a todas las funcionalidades del blog</li>
                <li>Posibilidad de comentar y interactuar con el contenido</li>
                <li>Suscripción automática a nuestro newsletter</li>
                <li>Experiencia personalizada según tus intereses</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Links -->
      <div class="text-center space-y-2">
        <div class="text-xs text-gray-500 space-y-1">
          <p>
            Al crear una cuenta, aceptas nuestros
            <NuxtLink to="/terms" class="text-blue-600 hover:text-blue-500">
              términos y condiciones
            </NuxtLink>
            y
            <NuxtLink to="/privacy" class="text-blue-600 hover:text-blue-500">
              política de privacidad
            </NuxtLink>
          </p>
        </div>
        <div>
          <NuxtLink
            to="/"
            class="text-sm text-gray-600 hover:text-gray-500"
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
import { useAuthStore } from '../../interface/stores/auth.store';
import RegisterForm from '../../interface/components/auth/RegisterForm.vue';
import type { RegisterPayload } from '../../interface/types/auth-store.types';

// Meta data
definePageMeta({
  layout: 'auth',
  middleware: 'guest'
});

// Head configuration
useHead({
  title: 'Crear Cuenta | BlogV2',
  meta: [
    {
      name: 'description',
      content: 'Crea tu cuenta en BlogV2 para acceder a contenido exclusivo, comentar posts y recibir nuestro newsletter.'
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
const handleRegisterSuccess = async (payload: RegisterPayload) => {
  successMessage.value = '¡Cuenta creada exitosamente! Bienvenido a BlogV2. Redirigiendo...';
  errorMessage.value = null;

  // Wait a moment to show success message
  await new Promise(resolve => setTimeout(resolve, 2000));

  // Redirect to intended destination or welcome page
  const welcomeUrl = '/welcome?newUser=true';
  await router.push(welcomeUrl);
};

const handleRegisterError = (error: string) => {
  errorMessage.value = error;
  successMessage.value = null;

  // Clear error after 10 seconds
  setTimeout(() => {
    errorMessage.value = null;
  }, 10000);
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

  // Check for messages from query params
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
    // For new registrations, go to welcome page
    const welcomeUrl = '/welcome?newUser=true';
    await router.push(welcomeUrl);
  }
});
</script>