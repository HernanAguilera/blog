<template>
  <NuxtLayout name="admin">
    <!-- Breadcrumb -->
    <AdminBreadcrumb :items="breadcrumbItems" />

    <!-- Header with title and action -->
    <AdminHeader title="Gestión de Posts" description="Administra todos los posts del blog">
      <template #actions>
        <NuxtLink
          to="/admin/posts/create"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Nuevo post
        </NuxtLink>
      </template>
    </AdminHeader>
    <!-- Quick stats -->
    <div v-if="postsStore && !postsStore.isLoading" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
      <!-- Total posts -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-md bg-gray-50 dark:bg-gray-700">
            <svg class="h-6 w-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total de Posts</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ totalStats.total }}</p>
          </div>
        </div>
      </div>

      <!-- Published posts -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-md bg-green-50">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Publicados</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ totalStats.published }}</p>
          </div>
        </div>
      </div>

      <!-- Draft posts -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-md bg-yellow-50">
            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Borradores</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ totalStats.drafts }}</p>
          </div>
        </div>
      </div>

      <!-- Scheduled posts -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-md bg-blue-50">
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Programados</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ totalStats.scheduled }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="postsStore && postsStore.isLoading" class="py-12">
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
        <p class="text-gray-500 dark:text-gray-400 mt-2">Cargando posts...</p>
      </div>
    </div>

    <!-- Posts list -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
      <div class="p-6">
        <PostList />
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { usePostsStore } from '~/interface/stores/posts.store';
import { useAuthStore } from '~/interface/stores/auth.store';
import PostList from '~/interface/components/posts/PostList.vue';
import AdminBreadcrumb from '~/interface/components/admin/AdminBreadcrumb.vue';
import AdminHeader from '~/interface/components/admin/AdminHeader.vue';

// Meta
definePageMeta({
  middleware: ['admin'],
  ssr: false // Disable SSR for admin pages to avoid hydration issues
});

// Store
const postsStore = usePostsStore();
const authStore = useAuthStore();

// Breadcrumb items
const breadcrumbItems = [
  { label: 'Panel Admin', to: '/admin' },
  { label: 'Posts' }
];

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

