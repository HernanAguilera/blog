<template>
  <nav>
    <div :class="mobile ? 'flex flex-col space-y-1' : 'flex space-x-8'">
      <!-- Enlaces principales -->
      <NuxtLink
        to="/"
        :class="[
          'text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors font-medium',
          mobile ? 'block px-3 py-2 rounded-md text-base hover:bg-gray-100 dark:hover:bg-gray-700' : 'text-sm'
        ]"
        @click="$emit('navigate')"
      >
        {{ $t('common.home') }}
      </NuxtLink>

      <NuxtLink
        to="/about"
        :class="[
          'text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors font-medium',
          mobile ? 'block px-3 py-2 rounded-md text-base hover:bg-gray-100 dark:hover:bg-gray-700' : 'text-sm'
        ]"
        @click="$emit('navigate')"
      >
        {{ $t('common.about') }}
      </NuxtLink>

      <NuxtLink
        to="/contact"
        :class="[
          'text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors font-medium',
          mobile ? 'block px-3 py-2 rounded-md text-base hover:bg-gray-100 dark:hover:bg-gray-700' : 'text-sm'
        ]"
        @click="$emit('navigate')"
      >
        {{ $t('common.contact') }}
      </NuxtLink>

      <!-- Separador solo en móvil -->
      <div
        v-if="mobile"
        class="border-t border-gray-200 dark:border-gray-600 my-2"
      ></div>

      <!-- Toggle tema oscuro - ClientOnly to avoid hydration mismatch -->
      <ClientOnly>
        <button
          @click="toggleDarkMode"
          :class="[
            'text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors font-medium flex items-center',
            mobile ? 'px-3 py-2 rounded-md text-base hover:bg-gray-100 dark:hover:bg-gray-700 justify-start' : 'text-sm'
          ]"
          :title="isDarkMode ? $t('nav.lightMode') : $t('nav.darkMode')"
        >
          <svg
            class="w-4 h-4 mr-1"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              v-if="isDarkMode"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
            ></path>
            <path
              v-else
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
            ></path>
          </svg>
          <span v-if="mobile">
            {{ isDarkMode ? $t('nav.lightMode') : $t('nav.darkMode') }}
          </span>
        </button>

        <!-- Fallback durante SSR - botón estático -->
        <template #fallback>
          <button
            :class="[
              'text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors font-medium flex items-center',
              mobile ? 'px-3 py-2 rounded-md text-base hover:bg-gray-100 dark:hover:bg-gray-700 justify-start' : 'text-sm'
            ]"
            :title="$t('nav.theme')"
          >
            <svg
              class="w-4 h-4 mr-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
              ></path>
            </svg>
            <span v-if="mobile">{{ $t('nav.theme') }}</span>
          </button>
        </template>
      </ClientOnly>

      <!-- Language Switcher -->
      <ClientOnly>
        <LanguageSwitcher :mobile="mobile" />
      </ClientOnly>
    </div>
  </nav>
</template>

<script setup lang="ts">
interface Props {
  mobile?: boolean
}

defineProps<Props>()
defineEmits<{
  navigate: []
}>()

// Dark mode toggle
const colorMode = useColorMode()
const isDarkMode = computed(() => colorMode.value === 'dark')

const toggleDarkMode = () => {
  colorMode.preference = isDarkMode.value ? 'light' : 'dark'
}
</script>
