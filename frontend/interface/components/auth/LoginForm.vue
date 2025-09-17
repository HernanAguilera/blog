<template>
  <form class="space-y-6" @submit.prevent="handleSubmit">
    <!-- Email Field -->
    <div>
      <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Email
      </label>
      <div class="mt-1">
        <input
          id="email"
          v-model="form.email.value"
          type="email"
          name="email"
          autocomplete="email"
          required
          :disabled="isSubmitting"
          class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed sm:text-sm"
          :class="{
            'border-red-300 focus:border-red-500 focus:ring-red-500': form.email.error
          }"
          placeholder="tu@email.com"
          @blur="validateField('email')"
          @input="clearFieldError('email')"
        >
      </div>
      <p v-if="form.email.error" class="mt-2 text-sm text-red-600">
        {{ form.email.error }}
      </p>
    </div>

    <!-- Password Field -->
    <div>
      <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Contraseña
      </label>
      <div class="mt-1 relative">
        <input
          id="password"
          v-model="form.password.value"
          :type="showPassword ? 'text' : 'password'"
          name="password"
          autocomplete="current-password"
          required
          :disabled="isSubmitting"
          class="appearance-none block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed sm:text-sm"
          :class="{
            'border-red-300 focus:border-red-500 focus:ring-red-500': form.password.error
          }"
          placeholder="Tu contraseña"
          @blur="validateField('password')"
          @input="clearFieldError('password')"
        >
        <button
          type="button"
          class="absolute inset-y-0 right-0 pr-3 flex items-center"
          :disabled="isSubmitting"
          @click="showPassword = !showPassword"
        >
          <svg
            v-if="showPassword"
            class="h-5 w-5 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          <svg
            v-else
            class="h-5 w-5 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
          </svg>
        </button>
      </div>
      <p v-if="form.password.error" class="mt-2 text-sm text-red-600">
        {{ form.password.error }}
      </p>
    </div>

    <!-- Remember Me -->
    <div class="flex items-center justify-between">
      <div class="flex items-center">
        <input
          id="remember-me"
          v-model="form.remember.value"
          name="remember-me"
          type="checkbox"
          :disabled="isSubmitting"
          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded disabled:cursor-not-allowed"
        >
        <label for="remember-me" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
          Recordarme
        </label>
      </div>

      <div class="text-sm">
        <NuxtLink
          to="/auth/forgot-password"
          class="font-medium text-blue-600 hover:text-blue-500"
        >
          ¿Olvidaste tu contraseña?
        </NuxtLink>
      </div>
    </div>

    <!-- Turnstile CAPTCHA -->
    <div ref="turnstileRef" class="flex justify-center"/>

    <!-- Error Messages -->
    <div v-if="generalError" class="rounded-md bg-red-50 p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">
            {{ generalError }}
          </h3>
        </div>
      </div>
    </div>

    <!-- Submit Button -->
    <div>
      <button
        type="submit"
        :disabled="!isFormValid || isSubmitting"
        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="isSubmitting" class="absolute left-0 inset-y-0 flex items-center pl-3">
          <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
          </svg>
        </span>
        {{ isSubmitting ? 'Iniciando sesión...' : 'Iniciar sesión' }}
      </button>
    </div>

    <!-- Social Login Divider -->
    <div class="mt-6">
      <div class="relative">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-300" />
        </div>
        <div class="relative flex justify-center text-sm">
          <span class="px-2 bg-white text-gray-500">O continúa con</span>
        </div>
      </div>
    </div>

    <!-- Social Login Buttons -->
    <div class="mt-6 grid grid-cols-3 gap-3">
      <button
        type="button"
        class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        @click="handleSocialLogin('google')"
      >
        <span class="sr-only">Iniciar sesión con Google</span>
        <svg class="w-5 h-5" viewBox="0 0 24 24">
          <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
          <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
          <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
          <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
      </button>

      <button
        type="button"
        class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        @click="handleSocialLogin('facebook')"
      >
        <span class="sr-only">Iniciar sesión con Facebook</span>
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
        </svg>
      </button>

      <button
        type="button"
        class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        @click="handleSocialLogin('twitter')"
      >
        <span class="sr-only">Iniciar sesión con Twitter</span>
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84" />
        </svg>
      </button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted, type Ref } from 'vue';
import { useAuthStore } from '../../stores/auth.store';
import type { LoginPayload } from '../../types/auth-store.types';

// Props
interface Props {
  redirectTo?: string;
}

withDefaults(defineProps<Props>(), {
  redirectTo: '/dashboard'
});

// Emits
const emit = defineEmits<{
  success: [payload: LoginPayload];
  error: [error: string];
  socialLogin: [provider: string];
}>();

// Composables
const authStore = useAuthStore();
const config = useRuntimeConfig();

// Form state
const form = reactive({
  email: {
    value: '' as string,
    error: null as string | null,
    touched: false,
    dirty: false
  },
  password: {
    value: '' as string,
    error: null as string | null,
    touched: false,
    dirty: false
  },
  remember: {
    value: false as boolean,
    error: null as string | null,
    touched: false,
    dirty: false
  }
});

// UI state
const showPassword = ref(false);
const turnstileRef: Ref<HTMLElement | null> = ref(null);
const turnstileToken = ref<string | null>(null);
const turnstileWidgetId = ref<string | null>(null);

// Computed
const isSubmitting = computed(() => authStore.isLoggingIn);
const generalError = computed(() => authStore.error);

const isFormValid = computed(() => {
  const emailValue = form.email.value;
  const passwordValue = form.password.value;

  // Type guards para asegurar que son strings y no están vacíos
  const isEmailValid = typeof emailValue === 'string' && emailValue.length > 0;
  const isPasswordValid = typeof passwordValue === 'string' && passwordValue.length > 0;

  return (
    isEmailValid &&
    isPasswordValid &&
    !form.email.error &&
    !form.password.error &&
    turnstileToken.value !== null
  );
});

// Form validation
const validateField = (fieldName: keyof typeof form) => {
  const field = form[fieldName];
  field.touched = true;

  switch (fieldName) {
    case 'email':
      const emailValue = field.value;
      if (typeof emailValue !== 'string' || emailValue.length === 0) {
        field.error = 'El email es requerido';
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
        field.error = 'Formato de email inválido';
      } else {
        field.error = null;
      }
      break;

    case 'password':
      const passwordValue = field.value;
      if (typeof passwordValue !== 'string' || passwordValue.length === 0) {
        field.error = 'La contraseña es requerida';
      } else if (passwordValue.length < 6) {
        field.error = 'La contraseña debe tener al menos 6 caracteres';
      } else {
        field.error = null;
      }
      break;
  }
};

const clearFieldError = (fieldName: keyof typeof form) => {
  form[fieldName].error = null;
  form[fieldName].dirty = true;
};

// Turnstile integration
const initTurnstile = () => {
  if (typeof window !== 'undefined' && window.turnstile && turnstileRef.value) {
    turnstileWidgetId.value = window.turnstile.render(turnstileRef.value, {
      sitekey: config.public.turnstileSiteKey,
      callback: (token: string) => {
        turnstileToken.value = token;
      },
      'error-callback': () => {
        turnstileToken.value = null;
      },
      'expired-callback': () => {
        turnstileToken.value = null;
      },
      theme: 'auto',
      size: 'normal'
    });
  }
};

const resetTurnstile = () => {
  if (window.turnstile && turnstileWidgetId.value) {
    window.turnstile.reset(turnstileWidgetId.value);
    turnstileToken.value = null;
  }
};

// Form submission
const handleSubmit = async () => {
  // Validate all fields
  validateField('email');
  validateField('password');

  if (!isFormValid.value) {
    return;
  }

  authStore.clearErrors();

  try {
    const result = await authStore.login({
      email: form.email.value as string,
      password: form.password.value as string,
      remember: form.remember.value as boolean,
      turnstileToken: turnstileToken.value || undefined
    });

    if (result.success) {
      emit('success', {
        email: form.email.value as string,
        password: form.password.value as string,
        remember: form.remember.value as boolean,
        turnstileToken: turnstileToken.value || undefined
      });
      // Redirect will be handled by the parent component or router
    } else {
      emit('error', result.message);
      resetTurnstile();
    }
  } catch (err) {
    const errorMessage = err instanceof Error ? err.message : 'Error inesperado';
    emit('error', errorMessage);
    resetTurnstile();
  }
};

// Social login
const handleSocialLogin = (provider: string) => {
  emit('socialLogin', provider);
};

// Lifecycle
onMounted(() => {
  // Load Turnstile script if not already loaded
  if (typeof window !== 'undefined' && !window.turnstile) {
    const script = document.createElement('script');
    script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js';
    script.async = true;
    script.defer = true;
    script.onload = initTurnstile;
    document.head.appendChild(script);
  } else {
    initTurnstile();
  }
});

onUnmounted(() => {
  if (window.turnstile && turnstileWidgetId.value) {
    window.turnstile.remove(turnstileWidgetId.value);
  }
});
</script>