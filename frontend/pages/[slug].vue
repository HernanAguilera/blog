<template>
  <NuxtLayout name="public">
    <div class="min-h-screen">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Loading state -->
        <div v-if="pending" class="animate-pulse">
          <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-4"></div>
          <div class="space-y-3">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-4/6"></div>
          </div>
        </div>

        <!-- Error state -->
        <div v-else-if="error || (!pending && !page)" class="text-center py-12">
          <svg class="w-24 h-24 text-red-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
          </svg>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
            Página no encontrada
          </h1>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            La página que buscas no existe o ha sido eliminada.
          </p>
          <NuxtLink
            to="/"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al inicio
          </NuxtLink>
        </div>

        <!-- Page content -->
        <article v-else-if="page">
          <!-- Breadcrumb -->
          <nav class="mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
              <li>
                <NuxtLink to="/" class="hover:text-blue-600 dark:hover:text-blue-400">
                  Inicio
                </NuxtLink>
              </li>
              <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
              </li>
              <li>
                <span class="text-gray-900 dark:text-white">{{ pageTitle }}</span>
              </li>
            </ol>
          </nav>

          <!-- Page Header -->
          <header class="mb-8">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-4 leading-tight">
              {{ pageTitle }}
            </h1>
          </header>

          <!-- Page Content -->
          <div class="prose prose-lg dark:prose-invert max-w-none">
            <div v-html="pageContent"></div>
          </div>

          <!-- Navigation -->
          <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
            <NuxtLink
              to="/"
              class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
              Volver al inicio
            </NuxtLink>
          </div>
        </article>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
const route = useRoute();
const { getPublishedPageBySlug } = usePages();

const slug = computed(() => route.params.slug as string);

const { data: page, error, pending } = await useAsyncData(
  `page-${slug.value}`,
  async () => {
    try {
      return await getPublishedPageBySlug(slug.value);
    } catch (err) {
      // Don't throw error during SSR - let the component handle it
      return null;
    }
  }
);

// Get translation based on locale (default to 'es')
const pageTranslation = computed(() => {
  if (!page.value) return null;
  return page.value.getTranslation('es') || page.value.getTranslations()[0];
});

const pageTitle = computed(() => pageTranslation.value?.getTitle().getValue() || '');
const pageContent = computed(() => pageTranslation.value?.getContent().getValue() || '');

// SEO meta tags
useHead({
  title: pageTitle.value,
  meta: [
    {
      name: 'description',
      content: pageTranslation.value?.getMetaDescription() || '',
    },
    {
      property: 'og:title',
      content: pageTitle.value,
    },
    {
      property: 'og:description',
      content: pageTranslation.value?.getMetaDescription() || '',
    },
    {
      property: 'og:type',
      content: 'website',
    },
  ],
});
</script>
