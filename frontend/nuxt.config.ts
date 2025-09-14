// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  // Modules
  modules: ['@pinia/nuxt', '@nuxtjs/tailwindcss', '@nuxt/eslint'],

  // Pinia configuration
  pinia: {
    storesDirs: ['~/domain/stores/**', '~/application/stores/**'],
  },

  // Tailwind CSS configuration
  tailwindcss: {
    exposeConfig: true,
    viewer: true,
  },

  // TypeScript configuration
  typescript: {
    strict: true,
    typeCheck: true,
  },

  // CSS configuration
  css: ['~/assets/css/main.css'],

  // Runtime config for API base URL
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
    },
  },

  // SSR configuration (can be disabled for SPA mode)
  ssr: true,

  // Nitro configuration for static generation
  nitro: {
    prerender: {
      routes: ['/'],
    },
  },
})
