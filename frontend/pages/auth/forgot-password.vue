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
          Recuperar contraseña
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
          Ingresa tu email y te enviaremos un enlace para restablecer tu contraseña
        </p>
      </div>

      <!-- Form -->
      <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow dark:shadow-gray-700 sm:rounded-lg sm:px-10">
        <div class="space-y-6">
          <p class="text-center text-gray-600 dark:text-gray-400">
            Esta funcionalidad estará disponible próximamente.
          </p>

          <!-- Back to Login -->
          <div class="text-center">
            <NuxtLink
              to="/auth/login"
              class="font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300"
            >
              ← Volver al login
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- Footer Links -->
      <div class="text-center space-y-2">
        <div>
          <NuxtLink
            to="/auth/register"
            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300"
          >
            ¿No tienes cuenta? Regístrate
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
// Meta data
definePageMeta({
  layout: 'auth',
  middleware: 'guest'
});

// Head configuration
useHead({
  title: 'Recuperar Contraseña | BlogV2',
  meta: [
    {
      name: 'description',
      content: 'Recupera tu contraseña de BlogV2. Te enviaremos un enlace para restablecer tu contraseña.'
    },
    {
      name: 'robots',
      content: 'noindex, nofollow'
    }
  ]
});
</script>