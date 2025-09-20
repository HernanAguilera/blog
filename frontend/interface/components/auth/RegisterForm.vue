<template>
  <form class="space-y-6" @submit.prevent="handleSubmit">
    <!-- Name Field -->
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Nombre completo
      </label>
      <div class="mt-1">
        <input
          id="name"
          v-model="form.name.value"
          type="text"
          name="name"
          autocomplete="name"
          required
          :disabled="isSubmitting"
          class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 dark:disabled:bg-gray-700 disabled:cursor-not-allowed sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
          :class="{
            'border-red-300 focus:border-red-500 focus:ring-red-500': form.name.error
          }"
          placeholder="Tu nombre completo"
          @blur="validateField('name')"
          @input="clearFieldError('name')"
        >
      </div>
      <p v-if="form.name.error" class="mt-2 text-sm text-red-600">
        {{ form.name.error }}
      </p>
    </div>

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
          class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 dark:disabled:bg-gray-700 disabled:cursor-not-allowed sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
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
          autocomplete="new-password"
          required
          :disabled="isSubmitting"
          class="appearance-none block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed sm:text-sm"
          :class="{
            'border-red-300 focus:border-red-500 focus:ring-red-500': form.password.error
          }"
          placeholder="Mínimo 8 caracteres"
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
      <!-- Password Strength Indicator -->
      <div v-if="form.password.value" class="mt-2">
        <div class="flex items-center space-x-1">
          <div class="text-xs text-gray-500">Fortaleza:</div>
          <div class="flex space-x-1">
            <div
              v-for="(strength, index) in passwordStrength"
              :key="index"
              class="h-2 w-6 rounded"
              :class="[
                strength ? getStrengthColor(passwordStrengthLevel) : 'bg-gray-200'
              ]"
            />
          </div>
          <div class="text-xs font-medium" :class="getStrengthTextColor(passwordStrengthLevel)">
            {{ getStrengthText(passwordStrengthLevel) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Confirm Password Field -->
    <div>
      <label for="confirmPassword" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Confirmar contraseña
      </label>
      <div class="mt-1">
        <input
          id="confirmPassword"
          v-model="form.confirmPassword.value"
          :type="showConfirmPassword ? 'text' : 'password'"
          name="confirmPassword"
          autocomplete="new-password"
          required
          :disabled="isSubmitting"
          class="appearance-none block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed sm:text-sm"
          :class="{
            'border-red-300 focus:border-red-500 focus:ring-red-500': form.confirmPassword.error
          }"
          placeholder="Repite tu contraseña"
          @blur="validateField('confirmPassword')"
          @input="clearFieldError('confirmPassword')"
        >
        <button
          type="button"
          class="absolute inset-y-0 right-0 pr-3 flex items-center"
          :disabled="isSubmitting"
          @click="showConfirmPassword = !showConfirmPassword"
        >
          <!-- Same eye icons as password field -->
        </button>
      </div>
      <p v-if="form.confirmPassword.error" class="mt-2 text-sm text-red-600">
        {{ form.confirmPassword.error }}
      </p>
    </div>

    <!-- Terms and Privacy -->
    <div>
      <div class="flex items-center">
        <input
          id="acceptTerms"
          v-model="form.acceptTerms.value"
          name="acceptTerms"
          type="checkbox"
          required
          :disabled="isSubmitting"
          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded disabled:cursor-not-allowed"
        >
        <label for="acceptTerms" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
          Acepto los
          <NuxtLink to="/terms" class="font-medium text-blue-600 hover:text-blue-500">
            términos y condiciones
          </NuxtLink>
          y la
          <NuxtLink to="/privacy" class="font-medium text-blue-600 hover:text-blue-500">
            política de privacidad
          </NuxtLink>
        </label>
      </div>
      <p v-if="form.acceptTerms.error" class="mt-2 text-sm text-red-600">
        {{ form.acceptTerms.error }}
      </p>
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
        {{ isSubmitting ? 'Creando cuenta...' : 'Crear cuenta' }}
      </button>
    </div>

    <!-- Login Link -->
    <div class="text-center">
      <span class="text-sm text-gray-600">
        ¿Ya tienes una cuenta?
        <NuxtLink to="/auth/login" class="font-medium text-blue-600 hover:text-blue-500">
          Inicia sesión
        </NuxtLink>
      </span>
    </div>
  </form>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted, type Ref } from 'vue';
import { useAuthStore } from '../../stores/auth.store';
import type { RegisterPayload } from '../../types/auth-store.types';
import type { FormField } from '../../types/form.types';

// Props
interface Props {
  redirectTo?: string;
}

const props = withDefaults(defineProps<Props>(), {
  redirectTo: '/profile'
});

// Emits
const emit = defineEmits<{
  success: [payload: RegisterPayload];
  error: [error: string];
}>();

// Composables
const authStore = useAuthStore();
const config = useRuntimeConfig();

// Form state
const form = reactive({
  name: {
    value: '',
    error: null,
    touched: false,
    dirty: false
  } as FormField<string>,
  email: {
    value: '',
    error: null,
    touched: false,
    dirty: false
  } as FormField<string>,
  password: {
    value: '',
    error: null,
    touched: false,
    dirty: false
  } as FormField<string>,
  confirmPassword: {
    value: '',
    error: null,
    touched: false,
    dirty: false
  } as FormField<string>,
  acceptTerms: {
    value: false,
    error: null,
    touched: false,
    dirty: false
  } as FormField<boolean>
});

// UI state
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const turnstileRef: Ref<HTMLElement | null> = ref(null);
const turnstileToken = ref<string | null>(null);
const turnstileWidgetId = ref<string | null>(null);

// Computed
const isSubmitting = computed(() => authStore.isRegistering);
const generalError = computed(() => authStore.error);

const passwordStrengthLevel = computed(() => {
  const password = form.password.value;
  if (!password) return 0;

  let score = 0;
  if (password.length >= 8) score++;
  if (/[A-Z]/.test(password)) score++;
  if (/[a-z]/.test(password)) score++;
  if (/[0-9]/.test(password)) score++;
  if (/[^A-Za-z0-9]/.test(password)) score++;

  return score;
});

const passwordStrength = computed(() => {
  const level = passwordStrengthLevel.value;
  return Array.from({ length: 5 }, (_, i) => i < level);
});

const isFormValid = computed(() => {
  return (
    form.name.value.trim() !== '' &&
    form.email.value.trim() !== '' &&
    form.password.value.trim() !== '' &&
    form.confirmPassword.value.trim() !== '' &&
    form.acceptTerms.value &&
    !form.name.error &&
    !form.email.error &&
    !form.password.error &&
    !form.confirmPassword.error &&
    !form.acceptTerms.error &&
    turnstileToken.value !== null
  );
});

// Password strength helpers
const getStrengthColor = (level: number) => {
  if (level <= 1) return 'bg-red-500';
  if (level <= 2) return 'bg-yellow-500';
  if (level <= 3) return 'bg-blue-500';
  return 'bg-green-500';
};

const getStrengthTextColor = (level: number) => {
  if (level <= 1) return 'text-red-600';
  if (level <= 2) return 'text-yellow-600';
  if (level <= 3) return 'text-blue-600';
  return 'text-green-600';
};

const getStrengthText = (level: number) => {
  if (level <= 1) return 'Débil';
  if (level <= 2) return 'Regular';
  if (level <= 3) return 'Buena';
  return 'Excelente';
};

// Form validation
const validateField = (fieldName: keyof typeof form) => {
  const field = form[fieldName];
  field.touched = true;

  switch (fieldName) {
    case 'name':
      if (typeof field.value === 'string') {
        if (!field.value.trim()) {
          field.error = 'El nombre es requerido';
        } else if (field.value.trim().length < 2) {
          field.error = 'El nombre debe tener al menos 2 caracteres';
        } else if (field.value.trim().length > 100) {
          field.error = 'El nombre no puede exceder 100 caracteres';
        } else {
          field.error = null;
        }
      }
      break;

    case 'email':
      if (typeof field.value === 'string') {
        if (!field.value.trim()) {
          field.error = 'El email es requerido';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
          field.error = 'Formato de email inválido';
        } else if (field.value.length > 254) {
          field.error = 'El email es demasiado largo';
        } else {
          field.error = null;
        }
      }
      break;

    case 'password':
      if (typeof field.value === 'string') {
        if (!field.value.trim()) {
          field.error = 'La contraseña es requerida';
        } else if (field.value.length < 8) {
          field.error = 'La contraseña debe tener al menos 8 caracteres';
        } else if (passwordStrengthLevel.value < 3) {
          field.error = 'La contraseña debe contener al menos 3 de: mayúsculas, minúsculas, números, caracteres especiales';
        } else {
          field.error = null;
        }

        // Re-validate confirm password if it has a value
        if (typeof form.confirmPassword.value === 'string' && form.confirmPassword.value && form.confirmPassword.touched) {
          validateField('confirmPassword');
        }
      }
      break;

    case 'confirmPassword':
      if (typeof field.value === 'string') {
        if (!field.value.trim()) {
          field.error = 'Debes confirmar la contraseña';
        } else if (field.value !== form.password.value) {
          field.error = 'Las contraseñas no coinciden';
        } else {
          field.error = null;
        }
      }
      break;

    case 'acceptTerms':
      if (!field.value) {
        field.error = 'Debes aceptar los términos y condiciones';
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

// Turnstile integration (same as LoginForm)
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
  validateField('name');
  validateField('email');
  validateField('password');
  validateField('confirmPassword');
  validateField('acceptTerms');

  if (!isFormValid.value) {
    return;
  }

  authStore.clearErrors();

  try {
    const result = await authStore.register({
      name: form.name.value,
      email: form.email.value,
      password: form.password.value,
      turnstileToken: turnstileToken.value || undefined
    });

    if (result.success) {
      emit('success', {
        name: form.name.value,
        email: form.email.value,
        password: form.password.value,
        turnstileToken: turnstileToken.value || undefined
      });
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

// Lifecycle
onMounted(() => {
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