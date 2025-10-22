<template>
  <div class="relative" ref="dropdownRef">
    <button
      @click="toggleDropdown"
      :class="[
        'text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors font-medium flex items-center',
        mobile ? 'px-3 py-2 rounded-md text-base hover:bg-gray-100 dark:hover:bg-gray-700 justify-start w-full' : 'text-sm'
      ]"
      :title="t('nav.language')"
    >
      <!-- Globe icon -->
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
          d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        ></path>
      </svg>
      <span>{{ currentLanguageName }}</span>
      <svg
        class="w-3 h-3 ml-1"
        :class="{ 'rotate-180': isOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
    </button>

    <!-- Dropdown menu -->
    <div
      v-show="isOpen"
      :class="[
        'absolute z-50 mt-2 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5',
        mobile ? 'left-0 w-full' : 'right-0 w-40'
      ]"
    >
      <div class="py-1" role="menu" aria-orientation="vertical">
        <button
          v-for="lang in availableLocales"
          :key="lang.code"
          @click="switchLanguage(lang.code)"
          :class="[
            'flex items-center w-full px-4 py-2 text-sm text-left transition-colors',
            locale === lang.code
              ? 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200'
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
          ]"
          role="menuitem"
        >
          <span class="mr-2">{{ getLanguageFlag(lang.code) }}</span>
          <span>{{ t(`languages.${lang.code}`) }}</span>
          <svg
            v-if="locale === lang.code"
            class="w-4 h-4 ml-auto"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
              clip-rule="evenodd"
            ></path>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'

interface Props {
  mobile?: boolean
}

defineProps<Props>()

const { locale, locales, t } = useI18n()
const isOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const availableLocales = computed(() => {
  return locales.value.map((loc: any) => {
    if (typeof loc === 'string') {
      return { code: loc, name: loc }
    }
    return { code: loc.code, name: loc.name || loc.code }
  })
})

const currentLanguageName = computed(() => {
  return t(`languages.${locale.value}`)
})

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const switchLanguage = async (langCode: string) => {
  isOpen.value = false

  // Use switchLocalePath to change language and navigate
  const switchLocalePath = useSwitchLocalePath()
  await navigateTo(switchLocalePath(langCode as 'es' | 'en' | 'pt'))
}

const getLanguageFlag = (langCode: string): string => {
  const flags: Record<string, string> = {
    'es': '🇪🇸',
    'en': '🇬🇧',
    'pt': '🇧🇷'
  }
  return flags[langCode] || '🌐'
}

// Close dropdown when clicking outside
const handleClickOutside = (event: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.rotate-180 {
  transform: rotate(180deg);
  transition: transform 0.2s;
}
</style>
