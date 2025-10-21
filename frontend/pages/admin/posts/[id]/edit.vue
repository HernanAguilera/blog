<template>
  <div class="edit-post-page">
    <!-- Page header -->
    <div class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-700">
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
        <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200 dark:lg:border-gray-600">
          <div class="flex-1 min-w-0">
            <!-- Breadcrumb -->
            <nav class="flex mb-4" aria-label="Breadcrumb">
              <ol class="flex items-center space-x-4">
                <li>
                  <div>
                    <NuxtLink to="/admin" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                      <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                      </svg>
                      <span class="sr-only">Inicio</span>
                    </NuxtLink>
                  </div>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <NuxtLink to="/admin/posts" class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                      Posts
                    </NuxtLink>
                  </div>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">
                      {{ currentPost ? currentPost.getTitle().value() : 'Editar' }}
                    </span>
                  </div>
                </li>
              </ol>
            </nav>

            <!-- Page title -->
            <div class="flex items-center">
              <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-gray-100 sm:text-3xl sm:truncate mr-4">
                Editar Post
              </h1>
              <PostStatusBadge
                v-if="currentPost"
                :status="currentPost.getStatus().value()"
                size="md"
              />
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              {{ currentPost ? `Última modificación: ${formatDate(currentPost.getUpdatedAt())}` : 'Cargando post...' }}
            </p>
          </div>

          <!-- Actions -->
          <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
            <!-- View public URL if published -->
            <NuxtLink
              v-if="currentPost?.isPublished() && publicUrl"
              :to="publicUrl"
              target="_blank"
              class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
              Ver publicado
            </NuxtLink>

            <NuxtLink
              to="/admin/posts"
              class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
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

    <!-- Loading state -->
    <div v-if="isLoading" class="py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <svg
            class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-600 mx-auto"
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
          <p class="text-gray-500 mt-2">Cargando post...</p>
        </div>
      </div>
    </div>

    <!-- Error state -->
    <div v-else-if="hasError" class="py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Error al cargar el post</h3>
          <p class="mt-1 text-sm text-gray-500">{{ errorMessage }}</p>
          <div class="mt-6">
            <button
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              @click="loadPost"
            >
              Intentar de nuevo
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div v-else-if="currentPost" class="py-8">
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
                Post actualizado exitosamente
              </h3>
              <div class="mt-4">
                <button
                  class="bg-green-50 px-2 py-1.5 rounded-md text-sm font-medium text-green-800 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600"
                  @click="showSuccessMessage = false"
                >
                  Cerrar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Editor -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
          <ClientOnly>
            <PostEditor
              :post-id="route.params.id as string"
              :initial-title="currentPost?.getTitle().value() || ''"
              :initial-content="currentPost?.getContent().value() || ''"
              :initial-meta-description="''"
              :auto-save="true"
              @save="handleSave"
              @change="handleChange"
              @status-change="handleStatusChange"
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
      </div>
    </div>

    <!-- Delete confirmation modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
          @click="showDeleteModal = false"
        />

        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 id="modal-title" class="text-lg leading-6 font-medium text-gray-900">
                Eliminar post
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  ¿Estás seguro de que quieres eliminar este post? Esta acción no se puede deshacer.
                </p>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
            <button
              :disabled="isDeleting"
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
              @click="confirmDelete"
            >
              {{ isDeleting ? 'Eliminando...' : 'Eliminar' }}
            </button>
            <button
              :disabled="isDeleting"
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
              @click="showDeleteModal = false"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { usePostsStore } from '~/interface/stores/posts.store';
import PostEditor from '~/interface/components/posts/PostEditor.vue';
import PostStatusBadge from '~/interface/components/posts/PostStatusBadge.vue';

// Meta
definePageMeta({
  middleware: ['admin'],
  layout: 'admin',
  ssr: false
});

// Composables
const route = useRoute();
const router = useRouter();
const postsStore = usePostsStore();

// Reactive state
const showSuccessMessage = ref(false);
const showDeleteModal = ref(false);
const isDeleting = ref(false);

// Computed
const currentPost = computed(() => postsStore.currentPost);
const isLoading = computed(() => postsStore.isLoading);
const hasError = computed(() => postsStore.hasError);
const errorMessage = computed(() => postsStore.error);

const publicUrl = computed(() => {
  if (!currentPost.value?.isPublished()) return '';
  return currentPost.value.getPublicUrl();
});

// Methods
const loadPost = async () => {
  try {
    const postId = route.params.id as string;
    await postsStore.fetchPost(postId);
  } catch (error) {
    console.error('Error loading post:', error);
  }
};

const handleSave = async (data: { title: string; content: string }) => {
  try {
    const postId = route.params.id as string;
    await postsStore.updatePost(postId, data);
    showSuccessMessage.value = true;

    // Hide success message after 3 seconds
    setTimeout(() => {
      showSuccessMessage.value = false;
    }, 3000);
  } catch (error) {
    console.error('Error saving post:', error);
  }
};

const handleChange = (data: { title: string; content: string }) => {
  // Handle content changes if needed
};

const handleStatusChange = async (status: string) => {
  try {
    showSuccessMessage.value = true;
    setTimeout(() => {
      showSuccessMessage.value = false;
    }, 3000);
  } catch (error) {
    console.error('Error changing status:', error);
  }
};

const handleDelete = () => {
  showDeleteModal.value = true;
};

const confirmDelete = async () => {
  try {
    isDeleting.value = true;
    const postId = route.params.id as string;

    await postsStore.deletePost(postId);

    // Redirect to posts list
    router.push('/admin/posts');
  } catch (error) {
    console.error('Error deleting post:', error);
    isDeleting.value = false;
  }
};

const formatDate = (date?: Date): string => {
  if (!date) return 'Fecha desconocida';

  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Lifecycle
onMounted(() => {
  loadPost();
});

// Page head
useHead({
  title: computed(() => {
    const title = currentPost.value?.getTitle().value();
    return title ? `Editar: ${title} - Panel de Administración` : 'Editar Post - Panel de Administración';
  }),
  meta: [
    {
      name: 'description',
      content: 'Editar post del blog'
    }
  ]
});

// Warn about unsaved changes when leaving the page
onBeforeRouteLeave(async (to, from, next) => {
  if (postsStore.hasUnsavedChanges) {
    const modal = useModal();
    const confirmed = await modal.confirm({
      title: 'Cambios sin guardar',
      message: 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?',
      variant: 'warning',
      confirmText: 'Sí, salir',
      cancelText: 'Cancelar'
    });

    if (confirmed) {
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
.edit-post-page {
  min-height: 100vh;
  @apply bg-gray-50 dark:bg-gray-900;
}
</style>