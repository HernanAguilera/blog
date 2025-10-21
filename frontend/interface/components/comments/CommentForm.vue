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
      alert('Comentario publicado exitosamente');
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
      alert('Comentario enviado. Será visible una vez aprobado por un moderador.');
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
    alert('Error: ' + (error.message || 'Error al enviar el comentario'));
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
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 2rem;
}

.comment-form__title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: #111827;
}

.comment-form__user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  margin-bottom: 1rem;
}

.comment-form__user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-weight: 600;
  font-size: 1rem;
  flex-shrink: 0;
}

.comment-form__user-details {
  display: flex;
  flex-direction: column;
  gap: 0.125rem;
}

.comment-form__user-name {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
}

.comment-form__user-email {
  font-size: 0.75rem;
  color: #6b7280;
}

.comment-form__form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.comment-form__anonymous-fields {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

@media (max-width: 640px) {
  .comment-form__anonymous-fields {
    grid-template-columns: 1fr;
  }
}

.comment-form__field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.comment-form__label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.comment-form__required {
  color: #ef4444;
}

.comment-form__input,
.comment-form__textarea {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
  transition: border-color 0.2s;
}

.comment-form__input:focus,
.comment-form__textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.comment-form__input--error,
.comment-form__textarea--error {
  border-color: #ef4444;
}

.comment-form__input:disabled,
.comment-form__textarea:disabled {
  background-color: #f9fafb;
  cursor: not-allowed;
}

.comment-form__textarea {
  resize: vertical;
  min-height: 100px;
}

.comment-form__meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.comment-form__error {
  color: #ef4444;
  font-size: 0.75rem;
}

.comment-form__counter {
  font-size: 0.75rem;
  color: #6b7280;
}

.comment-form__counter--error {
  color: #ef4444;
  font-weight: 600;
}

.comment-form__turnstile {
  margin: 1rem 0;
}

.comment-form__rate-limit {
  padding: 0.75rem;
  background-color: #fef3c7;
  border: 1px solid #fbbf24;
  border-radius: 6px;
  font-size: 0.875rem;
  color: #92400e;
}

.comment-form__actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}

.comment-form__button {
  padding: 0.75rem 1.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.comment-form__button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.comment-form__button--primary {
  background-color: #3b82f6;
  color: #ffffff;
  border: none;
}

.comment-form__button--primary:hover:not(:disabled) {
  background-color: #2563eb;
}

.comment-form__button--secondary {
  background-color: #ffffff;
  color: #374151;
  border: 1px solid #d1d5db;
}

.comment-form__button--secondary:hover:not(:disabled) {
  background-color: #f9fafb;
}
</style>
