// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },


  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@nuxt/eslint',
    '@nuxtjs/color-mode',
    '@nuxtjs/i18n'
  ],

  colorMode: {
    preference: 'system', // default theme
    dataValue: 'theme', // activate data-theme in <html> tag
    classSuffix: ''
  },

  i18n: {
    locales: [
      { code: 'es', iso: 'es-ES', name: 'Español', file: 'es.json' },
      { code: 'en', iso: 'en-US', name: 'English', file: 'en.json' },
      { code: 'pt', iso: 'pt-BR', name: 'Português', file: 'pt.json' }
    ],
    defaultLocale: 'es',
    strategy: 'prefix_except_default', // URLs: / (es), /en/, /pt/
    detectBrowserLanguage: {
      useCookie: true,
      cookieKey: 'i18n_redirected',
      redirectOn: 'root',
      alwaysRedirect: false,
      fallbackLocale: 'es'
    },
    langDir: 'locales/',
    vueI18n: './i18n.config.ts'
  },

  components: [
    {
      path: '~/interface/components',
      pathPrefix: false
    }
  ],

  imports: {
    dirs: [
      'interface/composables'
    ]
  },

  typescript: {
    strict: true,
    typeCheck: true
  },

  devServer: {
    host: '0.0.0.0'
  },

  vite: {
    server: {
      fs: {
        // Permitir acceso a directorios de pnpm (necesario cuando pnpm está instalado por Volta)
        strict: false
      }
    }
  },

  // Route rules for SSR
  nitro: {
    routeRules: {
      '/admin/**': { ssr: false },
      // Disable SSR for dynamic pages that fetch data from API
      // This avoids issues with API calls during SSR and hydration mismatches
      '/about': { ssr: false },
      '/contact': { ssr: false }
    }
  },

  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.API_BASE_URL || 'http://localhost:8000/api',
      baseUrl: process.env.BASE_URL || 'http://localhost:3000',
      turnstileSiteKey: process.env.TURNSTILE_SITE_KEY || ''
    }
  }
})
