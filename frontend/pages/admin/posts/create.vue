<template>
  <NuxtLayout name="admin-sidebar">
    <!-- Breadcrumb -->
    <AdminBreadcrumb :items="breadcrumbItems" />

    <!-- Header with title and action -->
    <AdminHeader title="Crear Nuevo Post" description="Escribe y publica un nuevo post para tu blog">
      <template #actions>
        <NuxtLink
          to="/admin/posts"
          class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 border-gray-300 dark:border-gray-600"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Volver a Posts
        </NuxtLink>
      </template>
    </AdminHeader>

    <!-- Main content -->
        <!-- Toast notifications will be handled by a global toast system -->

    <!-- Editor -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
          <ClientOnly>
            <PostEditor
              :auto-save="true"
              @save="handleSave"
              @change="handleChange"
            />
            <template #fallback>
              <div class="flex items-center justify-center h-96">
                <div class="text-center">
                  <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"/>
                  <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Cargando editor...</p>
                </div>
              </div>
            </template>
          </ClientOnly>
        </div>

        <!-- Manual save button (fallback) -->
        <div class="mt-6 flex justify-end">
          <button
            :disabled="!hasChanges || isSaving"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="handleManualSave"
          >
            <svg
              v-if="isSaving"
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              />
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              />
            </svg>
            {{ isSaving ? 'Guardando...' : 'Guardar borrador' }}
          </button>
        </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
import { ref, computed, getCurrentInstance } from 'vue';
import { useRouter, onBeforeRouteLeave  } from 'vue-router';
import { usePostsStore } from '~/interface/stores/posts.store';
import PostEditor from '~/interface/components/posts/PostEditor.vue';
import AdminBreadcrumb from '~/interface/components/admin/AdminBreadcrumb.vue';
import AdminHeader from '~/interface/components/admin/AdminHeader.vue';

// Meta
definePageMeta({
  middleware: ['auth', 'admin'],
  ssr: false
});

// Composables
const router = useRouter();
const instance = getCurrentInstance();
const toast = instance?.appContext.config.globalProperties.$toast;
const postsStore = import.meta.client ? usePostsStore() : null;

// Breadcrumb items
const breadcrumbItems = [
  { label: 'Panel Admin', to: '/admin' },
  { label: 'Posts', to: '/admin/posts' },
  { label: 'Crear' }
];

// Reactive state
const hasChanges = ref(false);
const postData = ref({
  title: '',
  content: ''
});

// Computed
const isSaving = computed(() => postsStore?.isSaving || false);

// Methods
const handleChange = (data: { title: string; content: string }) => {
  postData.value = data;
  hasChanges.value = !!(data.title || data.content);
};

const handleSave = async (data: { title: string; content: string }) => {
  await createPost(data);
};

const handleManualSave = async () => {
  if (!hasChanges.value) return;
  await createPost(postData.value);
};

const createPost = async (data: { title: string; content: string }) => {
  try {
    if (!postsStore) {
      toast?.error('Store not available. Please refresh the page.');
      return;
    }

    // Validate minimum requirements
    if (!data.title.trim()) {
      toast?.error('El título es requerido para crear un post.');
      return;
    }

    const newPost = await postsStore.createPost({
      title: data.title,
      content: data.content,
      status: 'draft'
    });

    if (newPost) {
      hasChanges.value = false;

      // Show success toast
      toast?.success('Post creado exitosamente como borrador');

      // Immediate redirect to posts list
      await router.push('/admin/posts');
    }
  } catch (error) {
    toast?.error(error instanceof Error ? error.message : 'Error desconocido al crear el post');
  }
};

// Page head
useHead({
  title: 'Crear Nuevo Post - Panel de Administración',
  meta: [
    {
      name: 'description',
      content: 'Crear un nuevo post para el blog'
    }
  ]
});

// Warn about unsaved changes when leaving the page
onBeforeRouteLeave((to, from, next) => {
  if (hasChanges.value) {
    const answer = window.confirm(
      'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?'
    );
    if (answer) {
      next();
    } else {
      next(false);
    }
  } else {
    next();
  }
});
</script>

<style scoped>
.create-post-page {
  min-height: 100vh;
  background-color: #f9fafb;
}
</style>