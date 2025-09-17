<template>
  <div class="create-post-page">
    <!-- Page header -->
    <div class="bg-white shadow">
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
        <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
          <div class="flex-1 min-w-0">
            <!-- Breadcrumb -->
            <nav class="flex mb-4" aria-label="Breadcrumb">
              <ol class="flex items-center space-x-4">
                <li>
                  <div>
                    <NuxtLink to="/admin" class="text-gray-400 hover:text-gray-500">
                      <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                      </svg>
                      <span class="sr-only">Inicio</span>
                    </NuxtLink>
                  </div>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <NuxtLink to="/admin/posts" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                      Posts
                    </NuxtLink>
                  </div>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">Crear</span>
                  </div>
                </li>
              </ol>
            </nav>

            <!-- Page title -->
            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
              Crear Nuevo Post
            </h1>
            <p class="mt-1 text-sm text-gray-500">
              Escribe y publica un nuevo post para tu blog
            </p>
          </div>

          <!-- Actions -->
          <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
            <NuxtLink
              to="/admin/posts"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Volver a Posts
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success message -->
        <div
          v-if="showSuccessMessage"
          class="rounded-md bg-green-50 p-4 mb-6"
        >
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-green-800">
                Post creado exitosamente
              </h3>
              <div class="mt-2 text-sm text-green-700">
                <p>El post ha sido guardado como borrador. Puedes continuar editándolo o publicarlo cuando esté listo.</p>
              </div>
              <div class="mt-4">
                <div class="-mx-2 -my-1.5 flex">
                  <NuxtLink
                    v-if="createdPostId"
                    :to="`/admin/posts/${createdPostId}/edit`"
                    class="bg-green-50 px-2 py-1.5 rounded-md text-sm font-medium text-green-800 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600"
                  >
                    Continuar editando
                  </NuxtLink>
                  <button
                    @click="showSuccessMessage = false"
                    class="ml-3 bg-green-50 px-2 py-1.5 rounded-md text-sm font-medium text-green-800 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600"
                  >
                    Cerrar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Error message -->
        <div
          v-if="errorMessage"
          class="rounded-md bg-red-50 p-4 mb-6"
        >
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                Error al crear el post
              </h3>
              <div class="mt-2 text-sm text-red-700">
                <p>{{ errorMessage }}</p>
              </div>
              <div class="mt-4">
                <button
                  @click="errorMessage = ''"
                  class="bg-red-50 px-2 py-1.5 rounded-md text-sm font-medium text-red-800 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600"
                >
                  Cerrar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Editor -->
        <div class="bg-white shadow rounded-lg">
          <PostEditor
            :auto-save="true"
            @save="handleSave"
            @change="handleChange"
          />
        </div>

        <!-- Manual save button (fallback) -->
        <div class="mt-6 flex justify-end">
          <button
            @click="handleManualSave"
            :disabled="!hasChanges || isSaving"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
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
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { usePostsStore } from '~/interface/stores/posts.store';
import PostEditor from '~/interface/components/posts/PostEditor.vue';

// Meta
definePageMeta({
  middleware: ['auth', 'admin'],
  layout: 'admin',
  ssr: false
});

// Composables
const router = useRouter();
const postsStore = usePostsStore();

// Reactive state
const showSuccessMessage = ref(false);
const errorMessage = ref('');
const createdPostId = ref<string | null>(null);
const hasChanges = ref(false);
const postData = ref({
  title: '',
  content: ''
});

// Computed
const isSaving = computed(() => postsStore.isSaving);

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
    errorMessage.value = '';

    // Validate minimum requirements
    if (!data.title.trim()) {
      errorMessage.value = 'El título es requerido para crear un post.';
      return;
    }

    const newPost = await postsStore.createPost({
      title: data.title,
      content: data.content,
      status: 'draft'
    });

    if (newPost) {
      hasChanges.value = false;
      createdPostId.value = newPost.getId().value();
      showSuccessMessage.value = true;

      // Scroll to top to show success message
      window.scrollTo({ top: 0, behavior: 'smooth' });

      // Optional: Auto-redirect after a delay
      setTimeout(() => {
        if (createdPostId.value) {
          router.push(`/admin/posts/${createdPostId.value}/edit`);
        }
      }, 3000);
    }
  } catch (error) {
    console.error('Error creating post:', error);
    errorMessage.value = error instanceof Error ? error.message : 'Error desconocido al crear el post';
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
  if (hasChanges.value && !showSuccessMessage.value) {
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