<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 flex justify-between items-center">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Páginas Estáticas</h1>
      <NuxtLink
        to="/admin/pages/create"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nueva Página
      </NuxtLink>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="animate-pulse bg-white dark:bg-gray-800 rounded-lg p-6">
        <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-1/4 mb-4"></div>
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
      </div>
    </div>

    <!-- Pages list -->
    <div v-else-if="pages.length > 0" class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
      <ul class="divide-y divide-gray-200 dark:divide-gray-700">
        <li v-for="page in pages" :key="page.getId().getValue()" class="hover:bg-gray-50 dark:hover:bg-gray-700">
          <div class="px-4 py-4 flex items-center sm:px-6">
            <div class="min-w-0 flex-1 sm:flex sm:items-center sm:justify-between">
              <div class="truncate">
                <div class="flex text-sm">
                  <p class="font-medium text-blue-600 dark:text-blue-400 truncate">
                    {{ getPageTitle(page) }}
                  </p>
                  <p class="ml-2 flex-shrink-0 font-normal text-gray-500 dark:text-gray-400">
                    /{{ page.getSlug().getValue() }}
                  </p>
                </div>
                <div class="mt-2 flex">
                  <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <span
                      :class="[
                        'inline-flex rounded-full px-2 text-xs font-semibold leading-5',
                        page.isPublished()
                          ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                          : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                      ]"
                    >
                      {{ page.isPublished() ? 'Publicado' : 'Borrador' }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="mt-4 flex-shrink-0 sm:mt-0 sm:ml-5">
                <div class="flex space-x-3">
                  <NuxtLink
                    :to="`/admin/pages/${page.getId().getValue()}`"
                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                  >
                    Editar
                  </NuxtLink>
                  <button
                    @click="handleDelete(page.getId().getValue())"
                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                  >
                    Eliminar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Empty state -->
    <div v-else class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg">
      <svg
        class="mx-auto h-12 w-12 text-gray-400"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
        />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay páginas</h3>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza creando una nueva página estática.</p>
      <div class="mt-6">
        <NuxtLink
          to="/admin/pages/create"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Nueva Página
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Page } from '~/domain/page/entities/Page';

definePageMeta({
  layout: 'admin',
  middleware: 'auth',
});

const { getAllPages, deletePage } = usePages();
const { confirm } = useModal();
const notification = useNotification();

const pages = ref<Page[]>([]);
const loading = ref(true);

const fetchPages = async () => {
  loading.value = true;
  try {
    pages.value = await getAllPages();
  } catch (error: any) {
    notification.error('Error al cargar las páginas: ' + error.message);
  } finally {
    loading.value = false;
  }
};

const getPageTitle = (page: { getTranslation: (locale: string) => any; getTranslations: () => any[] }): string => {
  const translation = page.getTranslation('es') || page.getTranslations()[0];
  return translation?.getTitle().getValue() || 'Sin título';
};

const handleDelete = async (id: string) => {
  const confirmed = await confirm({
    title: '¿Eliminar página?',
    message: 'Esta acción no se puede deshacer.',
    confirmText: 'Eliminar',
    variant: 'danger',
  });

  if (!confirmed) return;

  try {
    await deletePage(id);
    notification.success('Página eliminada correctamente');
    await fetchPages();
  } catch (error: any) {
    notification.error('Error al eliminar la página: ' + error.message);
  }
};

onMounted(() => {
  fetchPages();
});
</script>
