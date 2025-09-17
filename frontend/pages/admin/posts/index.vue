<template>
  <div class="admin-posts-page">
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
                    <span class="ml-4 text-sm font-medium text-gray-500">Posts</span>
                  </div>
                </li>
              </ol>
            </nav>

            <!-- Page title -->
            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
              Gestión de Posts
            </h1>
            <p class="mt-1 text-sm text-gray-500">
              Administra todos los posts del blog
            </p>
          </div>

          <!-- Actions -->
          <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
            <NuxtLink
              to="/admin/posts/create"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Nuevo Post
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Quick stats -->
        <div v-if="!postsStore.isLoading" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
          <!-- Total posts -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total de Posts
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ totalStats.total }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Published posts -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Publicados
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ totalStats.published }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Draft posts -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Borradores
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ totalStats.drafts }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Scheduled posts -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Programados
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ totalStats.scheduled }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading state -->
        <div v-if="postsStore.isLoading" class="py-12">
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
              <p class="text-gray-500 mt-2">Cargando posts...</p>
            </div>
          </div>
        </div>

        <!-- Posts list -->
        <div v-else class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <PostList />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { usePostsStore } from '~/interface/stores/posts.store';
import { useAuthStore } from '~/interface/stores/auth.store';
import PostList from '~/interface/components/posts/PostList.vue';

// Meta
definePageMeta({
  middleware: ['auth', 'admin'],
  layout: 'admin',
  ssr: false // Disable SSR for admin pages to avoid hydration issues
});

// Store
const postsStore = usePostsStore();
const authStore = useAuthStore();

// Computed
const totalStats = computed(() => {
  const posts = postsStore.posts || [];
  return {
    total: posts.length,
    published: posts.filter(p => p?.isPublished?.()).length,
    drafts: posts.filter(p => p?.isDraft?.()).length,
    scheduled: posts.filter(p => p?.isScheduled?.()).length,
    archived: posts.filter(p => p?.isArchived?.()).length
  };
});

// Methods
const loadInitialData = async () => {
  try {
    await postsStore.fetchPosts();
  } catch (error) {
    console.error('Error loading posts:', error);
  }
};

// Lifecycle
onMounted(() => {
  loadInitialData();
});

// Page head
useHead({
  title: 'Gestión de Posts - Panel de Administración',
  meta: [
    {
      name: 'description',
      content: 'Administra todos los posts del blog desde el panel de administración'
    }
  ]
});
</script>

<style scoped>
.admin-posts-page {
  min-height: 100vh;
  background-color: #f9fafb;
}
</style>