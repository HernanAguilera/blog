<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="titleId"
        @click="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" aria-hidden="true"></div>

        <!-- Modal Card -->
        <Transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="isOpen"
            ref="modalRef"
            class="relative z-10 w-full bg-white dark:bg-gray-800 rounded-lg shadow-xl"
            :class="sizeClasses"
            @click.stop
          >
            <!-- Header -->
            <div
              v-if="$slots.header || title || showCloseButton"
              class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700"
            >
              <slot name="header">
                <h3
                  :id="titleId"
                  class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                >
                  {{ title }}
                </h3>
              </slot>

              <!-- Close Button -->
              <button
                v-if="showCloseButton"
                type="button"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                aria-label="Cerrar modal"
                @click="handleClose"
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
              <slot></slot>
            </div>

            <!-- Footer -->
            <div
              v-if="$slots.footer"
              class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700"
            >
              <slot name="footer"></slot>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue'

interface Props {
  isOpen: boolean
  title?: string
  size?: 'sm' | 'md' | 'lg' | 'xl'
  closeOnBackdrop?: boolean
  closeOnEsc?: boolean
  showCloseButton?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  closeOnBackdrop: true,
  closeOnEsc: true,
  showCloseButton: true
})

const emit = defineEmits<{
  close: []
}>()

// Refs
const modalRef = ref<HTMLElement>()
const titleId = computed(() => `modal-title-${Math.random().toString(36).substring(7)}`)

// Size classes
const sizeClasses = computed(() => {
  const sizes = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl'
  }
  return sizes[props.size]
})

// Watch isOpen para manejar focus y scroll
watch(() => props.isOpen, async (newValue) => {
  if (newValue) {
    // Bloquear scroll del body
    document.body.style.overflow = 'hidden'

    // Enfocar el modal después de que el DOM se actualice
    await nextTick()
    if (modalRef.value) {
      const firstFocusable = modalRef.value.querySelector(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      ) as HTMLElement

      if (firstFocusable) {
        firstFocusable.focus()
      }
    }
  } else {
    document.body.style.overflow = ''
  }
})

// Manejar ESC key
const handleEscKey = (event: KeyboardEvent) => {
  if (props.closeOnEsc && event.key === 'Escape' && props.isOpen) {
    handleClose()
  }
}

// Manejar click en backdrop
const handleBackdropClick = () => {
  if (props.closeOnBackdrop) {
    handleClose()
  }
}

// Cerrar modal
const handleClose = () => {
  emit('close')
}

// Lifecycle
onMounted(() => {
  window.addEventListener('keydown', handleEscKey)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleEscKey)
  document.body.style.overflow = ''
})
</script>
