<template>
  <BaseModal
    :is-open="isOpen"
    :title="title"
    :size="size"
    close-on-backdrop
    close-on-esc
    show-close-button
    @close="handleClose"
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

    <!-- Footer con botón -->
    <template #footer>
      <button
        type="button"
        class="px-4 py-2 text-sm font-medium text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
        :class="buttonClasses"
        @click="handleClose"
      >
        {{ buttonText }}
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

const ErrorIcon = () => h('svg', {
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
  })
])

interface Props {
  isOpen: boolean
  title?: string
  message: string
  variant?: 'info' | 'success' | 'warning' | 'error'
  buttonText?: string
  size?: 'sm' | 'md' | 'lg' | 'xl'
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'info',
  buttonText: 'Aceptar',
  size: 'md'
})

const emit = defineEmits<{
  close: []
}>()

// Icon component según variante
const iconComponent = computed(() => {
  const icons = {
    info: InfoIcon,
    success: SuccessIcon,
    warning: WarningIcon,
    error: ErrorIcon
  }
  return icons[props.variant]
})

// Classes para el ícono
const iconColorClass = computed(() => {
  const colors = {
    info: 'text-blue-600',
    success: 'text-green-600',
    warning: 'text-yellow-600',
    error: 'text-red-600'
  }
  return colors[props.variant]
})

const iconBackgroundClass = computed(() => {
  const backgrounds = {
    info: 'bg-blue-100 dark:bg-blue-900/30',
    success: 'bg-green-100 dark:bg-green-900/30',
    warning: 'bg-yellow-100 dark:bg-yellow-900/30',
    error: 'bg-red-100 dark:bg-red-900/30'
  }
  return backgrounds[props.variant]
})

// Classes para el botón
const buttonClasses = computed(() => {
  const classes = {
    info: 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
    success: 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
    warning: 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
    error: 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  }
  return classes[props.variant]
})

// Handler
const handleClose = () => {
  emit('close')
}
</script>
