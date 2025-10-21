<template>
  <BaseModal
    :is-open="isOpen"
    :title="title"
    :size="size"
    :close-on-backdrop="!isLoading"
    :close-on-esc="!isLoading"
    :show-close-button="!isLoading"
    @close="handleCancel"
  >
    <!-- Body -->
    <div class="flex gap-4">
      <!-- Icon -->
      <div class="flex-shrink-0">
        <div
          class="flex items-center justify-center w-12 h-12 rounded-full"
          :class="iconBackgroundClass"
        >
          <component :is="iconComponent" class="w-6 h-6" :class="iconColorClass" />
        </div>
      </div>

      <!-- Message -->
      <div class="flex-1">
        <p class="text-sm text-gray-600 dark:text-gray-300">
          {{ message }}
        </p>
      </div>
    </div>

    <!-- Footer con botones -->
    <template #footer>
      <button
        type="button"
        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        :disabled="isLoading"
        @click="handleCancel"
      >
        {{ cancelText }}
      </button>

      <button
        type="button"
        class="px-4 py-2 text-sm font-medium text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        :class="buttonClasses"
        :disabled="isLoading"
        @click="handleConfirm"
      >
        <span v-if="isLoading" class="flex items-center gap-2">
          <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Procesando...
        </span>
        <span v-else>{{ confirmText }}</span>
      </button>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { computed, h } from 'vue'
import BaseModal from './BaseModal.vue'

// Icons como componentes funcionales simples
const InfoIcon = () => h('svg', {
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
  })
])

const SuccessIcon = () => h('svg', {
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
  })
])

const WarningIcon = () => h('svg', {
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
  })
])

const DangerIcon = () => h('svg', {
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
  })
])

interface Props {
  isOpen: boolean
  title?: string
  message: string
  variant?: 'info' | 'success' | 'warning' | 'danger'
  confirmText?: string
  cancelText?: string
  size?: 'sm' | 'md' | 'lg' | 'xl'
  isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'info',
  confirmText: 'Confirmar',
  cancelText: 'Cancelar',
  size: 'md',
  isLoading: false
})

const emit = defineEmits<{
  confirm: []
  cancel: []
}>()

// Icon component según variante
const iconComponent = computed(() => {
  const icons = {
    info: InfoIcon,
    success: SuccessIcon,
    warning: WarningIcon,
    danger: DangerIcon
  }
  return icons[props.variant]
})

// Classes para el ícono
const iconColorClass = computed(() => {
  const colors = {
    info: 'text-blue-600',
    success: 'text-green-600',
    warning: 'text-yellow-600',
    danger: 'text-red-600'
  }
  return colors[props.variant]
})

const iconBackgroundClass = computed(() => {
  const backgrounds = {
    info: 'bg-blue-100 dark:bg-blue-900/30',
    success: 'bg-green-100 dark:bg-green-900/30',
    warning: 'bg-yellow-100 dark:bg-yellow-900/30',
    danger: 'bg-red-100 dark:bg-red-900/30'
  }
  return backgrounds[props.variant]
})

// Classes para el botón de confirmar
const buttonClasses = computed(() => {
  const classes = {
    info: 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
    success: 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
    warning: 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
    danger: 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  }
  return classes[props.variant]
})

// Handlers
const handleConfirm = () => {
  if (!props.isLoading) {
    emit('confirm')
  }
}

const handleCancel = () => {
  if (!props.isLoading) {
    emit('cancel')
  }
}
</script>
