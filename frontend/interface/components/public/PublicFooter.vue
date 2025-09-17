<template>
  <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Logo y descripción -->
        <div class="col-span-1 md:col-span-1">
          <div class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            BlogV2
          </div>
          <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
            Un blog moderno construido con Laravel y Nuxt 3, siguiendo los principios de Arquitectura Limpia.
          </p>
        </div>

        <!-- Enlaces rápidos -->
        <div class="col-span-1">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white tracking-wider uppercase mb-4">
            Enlaces
          </h3>
          <ul class="space-y-3">
            <li>
              <NuxtLink
                to="/"
                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors text-sm"
              >
                Inicio
              </NuxtLink>
            </li>
            <li>
              <NuxtLink
                to="/about"
                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors text-sm"
              >
                Acerca de
              </NuxtLink>
            </li>
            <li>
              <NuxtLink
                to="/contact"
                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors text-sm"
              >
                Contacto
              </NuxtLink>
            </li>
          </ul>
        </div>

        <!-- Información adicional -->
        <div class="col-span-1">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white tracking-wider uppercase mb-4">
            Información
          </h3>
          <ul class="space-y-3">
            <li>
              <a
                href="#"
                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors text-sm"
              >
                Política de Privacidad
              </a>
            </li>
            <li>
              <a
                href="#"
                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors text-sm"
              >
                Términos de Servicio
              </a>
            </li>
            <li>
              <a
                href="/admin/login"
                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors text-sm"
              >
                Administración
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Separador y copyright -->
      <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="text-gray-600 dark:text-gray-400 text-sm">
            © {{ currentYear }} BlogV2. Todos los derechos reservados.
          </div>
          <div class="mt-4 md:mt-0">
            <p class="text-gray-600 dark:text-gray-400 text-sm">
              Hecho con ❤️ usando
              <span class="font-medium">Laravel</span> y
              <span class="font-medium">Nuxt 3</span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
const currentYear = new Date().getFullYear()
</script>