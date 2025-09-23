<template>
  <div class="view-post-page">
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
                      {{ currentPost ? currentPost.getTitle().value() : 'Ver' }}
                    </span>
                  </div>
                </li>
              </ol>
            </nav>

            <!-- Page title -->
            <div class="flex items-center">
              <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-gray-100 sm:text-3xl sm:truncate mr-4">
                Vista del Post
              </h1>
              <PostStatusBadge
                v-if="currentPost"
                :status="currentPost.getStatus().value()"
                size="md"
              />
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
            <!-- Edit button -->
            <NuxtLink
              :to="`/admin/posts/${route.params.id}/edit`"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
              </svg>
              Editar
            </NuxtLink>

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
          <p class="text-gray-500 dark:text-gray-400 mt-2">Cargando post...</p>
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
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Error al cargar el post</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ errorMessage }}</p>
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
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Post metadata -->
        <div class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-700 rounded-lg mb-8">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Información del Post</h3>
          </div>
          <div class="px-6 py-4">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                <dd class="mt-1">
                  <PostStatusBadge :status="currentPost.getStatus().value()" />
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Slug</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">
                  {{ currentPost.getSlug().value() }}
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Palabras</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ currentPost.getWordCount() }} palabras
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tiempo de lectura</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ readingTime }} minutos
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Creado</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ formatDate(currentPost.getCreatedAt()) }}
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Última modificación</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ formatDate(currentPost.getUpdatedAt()) }}
                </dd>
              </div>

              <div v-if="currentPost.getScheduledAt()">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Programado para</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ formatDate(currentPost.getScheduledAt()) }}
                </dd>
              </div>

              <div v-if="currentPost.getPublishedAt()">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Publicado</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ formatDate(currentPost.getPublishedAt()) }}
                </dd>
              </div>

              <div v-if="publicUrl" class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">URL pública</dt>
                <dd class="mt-1">
                  <NuxtLink
                    :to="publicUrl"
                    target="_blank"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-mono"
                  >
                    {{ fullPublicUrl }}
                  </NuxtLink>
                </dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- Post preview -->
        <div class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-700 rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Vista Previa del Contenido</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Así es como se verá el post para los lectores
            </p>
          </div>
          <div class="px-6 py-8">
            <article class="prose prose-lg max-w-none">
              <!-- Title -->
              <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                {{ currentPost.getTitle().value() }}
              </h1>

              <!-- Content -->
              <div
                class="prose-content"
                v-html="currentPost.getContent().value()"
              />
            </article>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { usePostsStore } from '~/interface/stores/posts.store';
import PostStatusBadge from '~/interface/components/posts/PostStatusBadge.vue';

// Meta
definePageMeta({
  middleware: ['admin'],
  layout: 'admin',
  ssr: false
});

// Composables
const route = useRoute();
const postsStore = usePostsStore();

// Computed
const currentPost = computed(() => postsStore.currentPost);
const isLoading = computed(() => postsStore.isLoading);
const hasError = computed(() => postsStore.hasError);
const errorMessage = computed(() => postsStore.error);

const publicUrl = computed(() => {
  if (!currentPost.value?.isPublished()) return '';
  return currentPost.value.getPublicUrl();
});

const fullPublicUrl = computed(() => {
  if (!publicUrl.value) return '';
  // In a real app, you'd use the actual domain
  return `https://yourdomain.com${publicUrl.value}`;
});

const readingTime = computed(() => {
  if (!currentPost.value) return 0;
  const words = currentPost.value.getWordCount();
  return Math.ceil(words / 200); // 200 words per minute average
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
    return title ? `${title} - Panel de Administración` : 'Ver Post - Panel de Administración';
  }),
  meta: [
    {
      name: 'description',
      content: 'Vista del post en el panel de administración'
    }
  ]
});
</script>

<style scoped>
.view-post-page {
  min-height: 100vh;
  @apply bg-gray-50 dark:bg-gray-900;
}

.prose-content {
  @apply text-gray-900 dark:text-gray-100 leading-relaxed;
}

.prose-content h1,
.prose-content h2,
.prose-content h3,
.prose-content h4,
.prose-content h5,
.prose-content h6 {
  @apply font-bold text-gray-900 dark:text-gray-100 mt-8 mb-4;
}

.prose-content h1 { @apply text-2xl; }
.prose-content h2 { @apply text-xl; }
.prose-content h3 { @apply text-lg; }

.prose-content p {
  @apply mb-4;
}

.prose-content ul,
.prose-content ol {
  @apply mb-4 pl-6;
}

.prose-content li {
  @apply mb-2;
}

.prose-content blockquote {
  @apply border-l-4 border-gray-300 dark:border-gray-600 pl-4 italic text-gray-600 dark:text-gray-300 mb-4;
}

.prose-content code {
  @apply bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-2 py-1 rounded text-sm font-mono;
}

.prose-content pre {
  @apply bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 p-4 rounded overflow-x-auto mb-4;
}

.prose-content a {
  @apply text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline;
}

.prose-content img {
  @apply max-w-full h-auto mb-4;
}
</style>