<template>
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nueva Página</h1>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Slug -->
      <div>
        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Slug (URL)
        </label>
        <input
          id="slug"
          v-model="formData.slug"
          type="text"
          required
          pattern="[a-z0-9\-]+"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
          placeholder="about, contact, etc."
        />
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Solo letras minúsculas, números y guiones. Ejemplo: about, contact
        </p>
      </div>

      <!-- Status -->
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Estado
        </label>
        <select
          id="status"
          v-model="formData.status"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
        >
          <option value="draft">Borrador</option>
          <option value="published">Publicado</option>
        </select>
      </div>

      <!-- Español Translation -->
      <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Español</h3>

        <div class="space-y-4">
          <div>
            <label for="title-es" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Título
            </label>
            <input
              id="title-es"
              v-model="formData.translations.es.title"
              type="text"
              required
              maxlength="255"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>

          <div>
            <label for="content-es" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Contenido
            </label>
            <textarea
              id="content-es"
              v-model="formData.translations.es.content"
              required
              rows="10"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono text-sm"
            ></textarea>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Puedes usar HTML
            </p>
          </div>

          <div>
            <label for="meta-es" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Meta Description (opcional)
            </label>
            <input
              id="meta-es"
              v-model="formData.translations.es.meta_description"
              type="text"
              maxlength="160"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>
        </div>
      </div>

      <!-- English Translation -->
      <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">English</h3>

        <div class="space-y-4">
          <div>
            <label for="title-en" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Title
            </label>
            <input
              id="title-en"
              v-model="formData.translations.en.title"
              type="text"
              required
              maxlength="255"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>

          <div>
            <label for="content-en" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Content
            </label>
            <textarea
              id="content-en"
              v-model="formData.translations.en.content"
              required
              rows="10"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono text-sm"
            ></textarea>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              You can use HTML
            </p>
          </div>

          <div>
            <label for="meta-en" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Meta Description (optional)
            </label>
            <input
              id="meta-en"
              v-model="formData.translations.en.meta_description"
              type="text"
              maxlength="160"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end space-x-3">
        <NuxtLink
          to="/admin/pages"
          class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
        >
          Cancelar
        </NuxtLink>
        <button
          type="submit"
          :disabled="submitting"
          class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ submitting ? 'Creando...' : 'Crear Página' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { PageStatus } from '~/domain/page/value-objects/PageStatus';

definePageMeta({
  layout: 'admin',
  middleware: 'auth',
});

const { createPage } = usePages();
const notification = useNotification();
const router = useRouter();

const submitting = ref(false);
const formData = ref({
  slug: '',
  status: PageStatus.DRAFT,
  translations: {
    es: {
      title: '',
      content: '',
      meta_description: '',
    },
    en: {
      title: '',
      content: '',
      meta_description: '',
    },
  },
});

const handleSubmit = async () => {
  submitting.value = true;
  try {
    await createPage(formData.value);
    notification.success('Página creada correctamente');
    router.push('/admin/pages');
  } catch (error: any) {
    notification.error('Error al crear la página: ' + error.message);
  } finally {
    submitting.value = false;
  }
};
</script>
