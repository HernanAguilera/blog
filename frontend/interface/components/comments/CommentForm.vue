<template>
  <div class="comment-form">
    <h3 class="comment-form__title">{{ title }}</h3>

    <!-- Información de usuario autenticado -->
    <div v-if="isAuthenticated" class="comment-form__user-info">
      <div class="comment-form__user-avatar">
        {{ userInitial }}
      </div>
      <div class="comment-form__user-details">
        <span class="comment-form__user-name">{{ userName }}</span>
        <span class="comment-form__user-email">{{ userEmail }}</span>
      </div>
    </div>

    <form @submit.prevent="handleSubmit" class="comment-form__form">
      <!-- Campos para usuarios anónimos -->
      <div v-if="!isAuthenticated" class="comment-form__anonymous-fields">
        <div class="comment-form__field">
          <label for="anonymous-name" class="comment-form__label">
            Nombre <span class="comment-form__required">*</span>
          </label>
          <input
            id="anonymous-name"
            v-model="anonymousName"
            type="text"
            class="comment-form__input"
            :class="{ 'comment-form__input--error': errors.name }"
            placeholder="Tu nombre"
            :disabled="isSubmitting"
            required
          />
          <span v-if="errors.name" class="comment-form__error">{{ errors.name }}</span>
        </div>

        <div class="comment-form__field">
          <label for="anonymous-email" class="comment-form__label">
            Email <span class="comment-form__required">*</span>
          </label>
          <input
            id="anonymous-email"
            v-model="anonymousEmail"
            type="email"
            class="comment-form__input"
            :class="{ 'comment-form__input--error': errors.email }"
            placeholder="tu@email.com"
            :disabled="isSubmitting"
            required
          />
          <span v-if="errors.email" class="comment-form__error">{{ errors.email }}</span>
        </div>
      </div>

      <!-- Campo de contenido -->
      <div class="comment-form__field">
        <label for="comment-content" class="comment-form__label">
          Comentario <span class="comment-form__required">*</span>
        </label>
        <textarea
          id="comment-content"
          v-model="content"
          class="comment-form__textarea"
          :class="{ 'comment-form__textarea--error': errors.content }"
          placeholder="Escribe tu comentario..."
          rows="4"
          :disabled="isSubmitting"
          required
        ></textarea>
        <div class="comment-form__meta">
          <span v-if="errors.content" class="comment-form__error">{{ errors.content }}</span>
          <span class="comment-form__counter" :class="{ 'comment-form__counter--error': content.length > 2000 }">
            {{ content.length }} / 2000
          </span>
        </div>
      </div>

      <!-- Turnstile widget para anónimos -->
      <div v-if="!isAuthenticated" ref="turnstileContainer" class="comment-form__turnstile"></div>

      <!-- Rate limiting info -->
      <div v-if="rateLimitRemaining > 0" class="comment-form__rate-limit">
        Por favor espera {{ rateLimitRemaining }} segundos antes de comentar nuevamente.
      </div>

      <!-- Botones -->
      <div class="comment-form__actions">
        <button
          v-if="showCancel"
          type="button"
          class="comment-form__button comment-form__button--secondary"
          :disabled="isSubmitting"
          @click="handleCancel"
        >
          Cancelar
        </button>
        <button
          type="submit"
          class="comment-form__button comment-form__button--primary"
          :disabled="isSubmitting || rateLimitRemaining > 0"
        >
          {{ isSubmitting ? 'Enviando...' : 'Enviar comentario' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '../../stores/auth.store';
import { useComments } from '../../composables/useComments';

interface Props {
  postSlug: string;
  parentId?: string;
  title?: string;
  showCancel?: boolean;
}

interface Emits {
  (e: 'comment-created'): void;
  (e: 'cancel'): void;
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Dejar un comentario',
  showCancel: false,
});

const emit = defineEmits<Emits>();

const authStore = useAuthStore();
const { createComment, createAnonymousComment } = useComments(props.postSlug);

// State
const content = ref('');
const anonymousName = ref('');
const anonymousEmail = ref('');
const isSubmitting = ref(false);
const errors = ref<{ name?: string; email?: string; content?: string }>({});
const turnstileToken = ref<string | null>(null);
const turnstileWidget = ref<any>(null);
const turnstileContainer = ref<HTMLElement | null>(null);
const rateLimitRemaining = ref(0);
let rateLimitInterval: NodeJS.Timeout | null = null;

// Computed
const isAuthenticated = computed(() => authStore.isAuthenticated);

const userName = computed(() => authStore.userName || 'Usuario');

const userEmail = computed(() => authStore.userEmail || '');

const userInitial = computed(() => {
  const name = authStore.userName || 'U';
  return name.charAt(0).toUpperCase();
});

// Methods
const validateForm = (): boolean => {
  errors.value = {};

  // Validar contenido
  if (!content.value.trim()) {
    errors.value.content = 'El comentario no puede estar vacío';
    return false;
  }

  if (content.value.length < 3) {
    errors.value.content = 'El comentario debe tener al menos 3 caracteres';
    return false;
  }

  if (content.value.length > 2000) {
    errors.value.content = 'El comentario no puede exceder 2000 caracteres';
    return false;
  }

  // Validar campos anónimos
  if (!isAuthenticated.value) {
    if (!anonymousName.value.trim()) {
      errors.value.name = 'El nombre es requerido';
      return false;
    }

    if (anonymousName.value.length < 2) {
      errors.value.name = 'El nombre debe tener al menos 2 caracteres';
      return false;
    }

    if (anonymousName.value.length > 100) {
      errors.value.name = 'El nombre no puede exceder 100 caracteres';
      return false;
    }

    if (!anonymousEmail.value.trim()) {
      errors.value.email = 'El email es requerido';
      return false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(anonymousEmail.value)) {
      errors.value.email = 'El email no es válido';
      return false;
    }

    if (!turnstileToken.value) {
      errors.value.content = 'Por favor completa la verificación de seguridad';
      return false;
    }
  }

  return true;
};

const handleSubmit = async () => {
  if (!validateForm()) {
    return;
  }

  isSubmitting.value = true;

  try {
    if (isAuthenticated.value) {
      // Usuario autenticado
      await createComment({
        postSlug: props.postSlug,
        content: content.value,
        parentId: props.parentId,
      });

      const modal = useModal();
      await modal.alert({
        title: 'Éxito',
        message: 'Comentario publicado exitosamente',
        variant: 'success'
      });
    } else {
      // Usuario anónimo
      await createAnonymousComment({
        postSlug: props.postSlug,
        content: content.value,
        anonymousName: anonymousName.value,
        anonymousEmail: anonymousEmail.value,
        parentId: props.parentId,
        turnstileToken: turnstileToken.value!,
      });

      const modal = useModal();
      await modal.alert({
        title: 'Comentario enviado',
        message: 'Tu comentario será visible una vez aprobado por un moderador.',
        variant: 'info'
      });
    }

    // Emitir evento de éxito
    emit('comment-created');

    // Resetear formulario
    content.value = '';
    if (!isAuthenticated.value) {
      anonymousName.value = '';
      anonymousEmail.value = '';
      turnstileToken.value = null;
      resetTurnstile();
    }

    // Iniciar rate limiting (20 segundos)
    startRateLimitCountdown(20);
  } catch (error: any) {
    console.error('Error submitting comment:', error);
    errors.value.content = error.message || 'Error al enviar el comentario';

    const modal = useModal();
    await modal.alert({
      title: 'Error',
      message: error.message || 'Error al enviar el comentario',
      variant: 'error'
    });
  } finally {
    isSubmitting.value = false;
  }
};

const handleCancel = () => {
  emit('cancel');
  resetForm();
};

const resetForm = () => {
  content.value = '';
  anonymousName.value = '';
  anonymousEmail.value = '';
  errors.value = {};
  turnstileToken.value = null;
  if (!isAuthenticated.value) {
    resetTurnstile();
  }
};

const startRateLimitCountdown = (seconds: number) => {
  rateLimitRemaining.value = seconds;

  // Guardar timestamp en localStorage
  const expiresAt = Date.now() + seconds * 1000;
  localStorage.setItem('comment_rate_limit_expires', expiresAt.toString());

  if (rateLimitInterval) {
    clearInterval(rateLimitInterval);
  }

  rateLimitInterval = setInterval(() => {
    rateLimitRemaining.value--;
    if (rateLimitRemaining.value <= 0) {
      if (rateLimitInterval) {
        clearInterval(rateLimitInterval);
      }
      localStorage.removeItem('comment_rate_limit_expires');
    }
  }, 1000);
};

const checkRateLimitFromStorage = () => {
  const expiresAt = localStorage.getItem('comment_rate_limit_expires');
  if (expiresAt) {
    const remaining = Math.ceil((parseInt(expiresAt) - Date.now()) / 1000);
    if (remaining > 0) {
      startRateLimitCountdown(remaining);
    } else {
      localStorage.removeItem('comment_rate_limit_expires');
    }
  }
};

// Turnstile functions
const initTurnstile = () => {
  if (isAuthenticated.value || !turnstileContainer.value) {
    return;
  }

  // TODO: Integrar Cloudflare Turnstile en producción
  // Por ahora, generamos un token dummy para testing
  if (process.dev) {
    // En desarrollo, generar token dummy automáticamente
    turnstileToken.value = 'development-token-' + Date.now();

    // Mostrar mensaje informativo
    if (turnstileContainer.value) {
      turnstileContainer.value.innerHTML =
        '<div style="padding: 0.5rem; background: #fef3c7; border: 1px solid #fbbf24; border-radius: 4px; font-size: 0.875rem;">' +
        '⚠️ Modo desarrollo: Turnstile deshabilitado' +
        '</div>';
    }
  } else {
    // En producción, usar Turnstile real
    // if (window.turnstile) {
    //   turnstileWidget.value = window.turnstile.render(turnstileContainer.value, {
    //     sitekey: 'YOUR_SITE_KEY',
    //     callback: (token: string) => {
    //       turnstileToken.value = token;
    //     },
    //   });
    // }

    // Por ahora, en producción también usamos token dummy
    turnstileToken.value = 'testing-token-' + Date.now();
    if (turnstileContainer.value) {
      turnstileContainer.value.innerHTML =
        '<div style="padding: 0.5rem; background: #dbeafe; border: 1px solid #3b82f6; border-radius: 4px; font-size: 0.875rem;">' +
        'ℹ️ Verificación de seguridad pendiente de configurar' +
        '</div>';
    }
  }
};

const resetTurnstile = () => {
  // TODO: Implementar reset de Turnstile real
  // if (window.turnstile && turnstileWidget.value) {
  //   window.turnstile.reset(turnstileWidget.value);
  // }

  // Por ahora, regenerar token dummy
  turnstileToken.value = null;
  initTurnstile();
};

// Lifecycle
onMounted(() => {
  checkRateLimitFromStorage();
  if (!isAuthenticated.value) {
    initTurnstile();
  }
});

onUnmounted(() => {
  if (rateLimitInterval) {
    clearInterval(rateLimitInterval);
  }
});
</script>

<style scoped>
.comment-form {
  @apply bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-8;
}

.comment-form__title {
  @apply text-xl font-semibold mb-6 text-gray-900 dark:text-gray-100;
}

.comment-form__user-info {
  @apply flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-md mb-4;
}

.comment-form__user-avatar {
  @apply w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-base flex-shrink-0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.comment-form__user-details {
  @apply flex flex-col gap-0.5;
}

.comment-form__user-name {
  @apply font-semibold text-gray-900 dark:text-gray-100 text-sm;
}

.comment-form__user-email {
  @apply text-xs text-gray-500 dark:text-gray-400;
}

.comment-form__form {
  @apply flex flex-col gap-4;
}

.comment-form__anonymous-fields {
  @apply grid grid-cols-2 gap-4;
}

@media (max-width: 640px) {
  .comment-form__anonymous-fields {
    @apply grid-cols-1;
  }
}

.comment-form__field {
  @apply flex flex-col gap-2;
}

.comment-form__label {
  @apply text-sm font-medium text-gray-700 dark:text-gray-300;
}

.comment-form__required {
  @apply text-red-500;
}

.comment-form__input,
.comment-form__textarea {
  @apply px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors;
}

.comment-form__input:focus,
.comment-form__textarea:focus {
  @apply outline-none border-blue-500 dark:border-blue-400 ring-2 ring-blue-500/20 dark:ring-blue-400/20;
}

.comment-form__input--error,
.comment-form__textarea--error {
  @apply border-red-500 dark:border-red-400;
}

.comment-form__input:disabled,
.comment-form__textarea:disabled {
  @apply bg-gray-50 dark:bg-gray-800 cursor-not-allowed opacity-60;
}

.comment-form__textarea {
  @apply resize-y min-h-[100px];
}

.comment-form__meta {
  @apply flex justify-between items-center;
}

.comment-form__error {
  @apply text-red-500 dark:text-red-400 text-xs;
}

.comment-form__counter {
  @apply text-xs text-gray-500 dark:text-gray-400;
}

.comment-form__counter--error {
  @apply text-red-500 dark:text-red-400 font-semibold;
}

.comment-form__turnstile {
  @apply my-4;
}

.comment-form__rate-limit {
  @apply px-3 py-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-md text-sm text-yellow-800 dark:text-yellow-300;
}

.comment-form__actions {
  @apply flex gap-3 justify-end;
}

.comment-form__button {
  @apply px-6 py-2 text-sm font-medium rounded-md cursor-pointer transition-all;
}

.comment-form__button:disabled {
  @apply opacity-50 cursor-not-allowed;
}

.comment-form__button--primary {
  @apply bg-blue-600 dark:bg-blue-500 text-white border-0;
}

.comment-form__button--primary:hover:not(:disabled) {
  @apply bg-blue-700 dark:bg-blue-600;
}

.comment-form__button--secondary {
  @apply bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600;
}

.comment-form__button--secondary:hover:not(:disabled) {
  @apply bg-gray-50 dark:bg-gray-600;
}
</style>
