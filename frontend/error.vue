<template>
  <NuxtLayout name="public">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full text-center">
        <!-- Error Icon -->
        <svg
          class="w-24 h-24 text-red-300 dark:text-red-400 mx-auto mb-6"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"
          />
        </svg>

        <!-- Error Code -->
        <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-2">
          {{ error?.statusCode || 500 }}
        </h1>

        <!-- Error Title -->
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">
          {{ getErrorTitle(error?.statusCode) }}
        </h2>

        <!-- Error Message -->
        <p class="text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
          {{ getErrorMessage(error?.statusCode, error?.statusMessage) }}
        </p>

        <!-- Action Buttons -->
        <div class="space-y-3 sm:space-y-0 sm:space-x-3 sm:flex sm:justify-center">
          <button
            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            @click="handleRetry"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Intentar de nuevo
          </button>

          <NuxtLink
            to="/"
            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al inicio
          </NuxtLink>
        </div>

        <!-- Additional Help -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Si el problema persiste, puedes
            <NuxtLink
              to="/contacto"
              class="text-blue-600 dark:text-blue-400 hover:underline"
            >
              contactarnos
            </NuxtLink>
            para obtener ayuda.
          </p>
        </div>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
// Props del error
interface ErrorProps {
  error?: {
    statusCode?: number
    statusMessage?: string
    message?: string
  }
}

const props = defineProps<ErrorProps>()

// SEO composable
const { setErrorSEO } = useSEO()

// Configurar SEO para la página de error
watchEffect(() => {
  const statusCode = props.error?.statusCode || 500
  setErrorSEO(statusCode)
})

// Métodos utilitarios
const getErrorTitle = (statusCode?: number): string => {
  const titles: Record<number, string> = {
    400: 'Solicitud incorrecta',
    401: 'No autorizado',
    403: 'Acceso denegado',
    404: 'Página no encontrada',
    422: 'Datos no válidos',
    429: 'Demasiadas solicitudes',
    500: 'Error interno del servidor',
    502: 'Puerta de enlace incorrecta',
    503: 'Servicio no disponible',
    504: 'Tiempo de espera agotado'
  }

  return titles[statusCode || 500] || 'Ha ocurrido un error'
}

const getErrorMessage = (statusCode?: number, statusMessage?: string): string => {
  if (statusMessage) return statusMessage

  const messages: Record<number, string> = {
    400: 'La solicitud no pudo ser procesada debido a un error en los datos enviados.',
    401: 'Necesitas autenticarte para acceder a esta página.',
    403: 'No tienes permisos para acceder a esta página.',
    404: 'La página que buscas no existe o ha sido movida.',
    422: 'Los datos enviados no cumplen con los requisitos necesarios.',
    429: 'Has realizado demasiadas solicitudes. Por favor, espera un momento antes de intentar de nuevo.',
    500: 'Ha ocurrido un error interno en el servidor. Nuestro equipo ha sido notificado.',
    502: 'Hay un problema con la conexión al servidor. Por favor, intenta más tarde.',
    503: 'El servicio no está disponible temporalmente. Por favor, intenta más tarde.',
    504: 'El servidor tardó demasiado en responder. Por favor, intenta de nuevo.'
  }

  return messages[statusCode || 500] || 'Ha ocurrido un error inesperado. Por favor, intenta de nuevo.'
}

// Manejar reintento
const handleRetry = () => {
  // Recargar la página
  if (import.meta.client) {
    window.location.reload()
  }
}
</script>