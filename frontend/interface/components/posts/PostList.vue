<template>
  <div class="post-list">
    <!-- Header with filters and actions -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">Posts</h2>
          <p class="text-sm text-gray-600 mt-1">
            {{ totalPosts }} posts en total
          </p>
        </div>

        <div class="flex items-center space-x-3">
          <!-- Create new post button -->
          <NuxtLink
            to="/admin/posts/create"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo post
          </NuxtLink>

          <!-- View mode toggle -->
          <div class="flex rounded-md shadow-sm">
            <button
              @click="viewMode = 'grid'"
              :class="[
                'px-3 py-2 text-sm font-medium rounded-l-md border',
                viewMode === 'grid'
                  ? 'bg-blue-50 border-blue-200 text-blue-700'
                  : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'
              ]"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
            </button>
            <button
              @click="viewMode = 'list'"
              :class="[
                'px-3 py-2 text-sm font-medium rounded-r-md border-t border-r border-b',
                viewMode === 'list'
                  ? 'bg-blue-50 border-blue-200 text-blue-700'
                  : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'
              ]"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
        <!-- Search -->
        <div class="flex-1">
          <label for="search" class="sr-only">Buscar posts</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              id="search"
              v-model="searchQuery"
              @input="debouncedSearch"
              type="text"
              placeholder="Buscar por título o contenido..."
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
            />
          </div>
        </div>

        <!-- Status filter -->
        <div class="min-w-0">
          <label for="status-filter" class="sr-only">Filtrar por estado</label>
          <select
            id="status-filter"
            v-model="statusFilter"
            @change="applyFilters"
            class="block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md"
          >
            <option value="">Todos los estados</option>
            <option value="draft">Borradores</option>
            <option value="scheduled">Programados</option>
            <option value="published">Publicados</option>
            <option value="archived">Archivados</option>
          </select>
        </div>

        <!-- Sort options -->
        <div class="min-w-0">
          <label for="sort-filter" class="sr-only">Ordenar por</label>
          <select
            id="sort-filter"
            v-model="sortBy"
            @change="applyFilters"
            class="block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md"
          >
            <option value="updated_at">Última modificación</option>
            <option value="created_at">Fecha de creación</option>
            <option value="published_at">Fecha de publicación</option>
            <option value="title">Título</option>
          </select>
        </div>

        <!-- Clear filters -->
        <button
          v-if="hasActiveFilters"
          @click="clearFilters"
          class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          Limpiar
        </button>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="isLoading" class="text-center py-12">
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

    <!-- Error state -->
    <div v-else-if="hasError" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Error al cargar posts</h3>
      <p class="mt-1 text-sm text-gray-500">{{ errorMessage }}</p>
      <div class="mt-6">
        <button
          @click="retryLoad"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Intentar de nuevo
        </button>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="posts.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">
        {{ hasActiveFilters ? 'No se encontraron posts' : 'No hay posts aún' }}
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ hasActiveFilters ? 'Intenta cambiar los filtros de búsqueda.' : 'Comienza creando tu primer post.' }}
      </p>
      <div class="mt-6" v-if="!hasActiveFilters">
        <NuxtLink
          to="/admin/posts/create"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Crear primer post
        </NuxtLink>
      </div>
    </div>

    <!-- Posts content -->
    <div v-else>
      <!-- Grid view -->
      <div
        v-if="viewMode === 'grid'"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        <PostCard
          v-for="post in posts"
          :key="post.getId().value()"
          :post="post"
          @preview="handlePreview"
          @status-change="handleStatusChange"
          @delete="handleDelete"
        />
      </div>

      <!-- List view -->
      <div v-else class="space-y-4">
        <PostCard
          v-for="post in posts"
          :key="post.getId().value()"
          :post="post"
          :compact="true"
          @preview="handlePreview"
          @status-change="handleStatusChange"
          @delete="handleDelete"
        />
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="mt-8">
        <nav class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="goToPage(pagination.current_page - 1)"
              :disabled="pagination.current_page <= 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Anterior
            </button>
            <button
              @click="goToPage(pagination.current_page + 1)"
              :disabled="pagination.current_page >= pagination.last_page"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Siguiente
            </button>
          </div>

          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Mostrando
                <span class="font-medium">{{ (pagination.current_page - 1) * pagination.per_page + 1 }}</span>
                a
                <span class="font-medium">{{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}</span>
                de
                <span class="font-medium">{{ pagination.total }}</span>
                resultados
              </p>
            </div>

            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <!-- Previous page -->
                <button
                  @click="goToPage(pagination.current_page - 1)"
                  :disabled="pagination.current_page <= 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>

                <!-- Page numbers -->
                <button
                  v-for="page in visiblePages"
                  :key="page"
                  @click="goToPage(page)"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    page === pagination.current_page
                      ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                  ]"
                >
                  {{ page }}
                </button>

                <!-- Next page -->
                <button
                  @click="goToPage(pagination.current_page + 1)"
                  :disabled="pagination.current_page >= pagination.last_page"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
              </nav>
            </div>
          </div>
        </nav>
      </div>
    </div>

    <!-- Preview modal -->
    <div
      v-if="showPreviewModal && previewPost"
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
          @click="closePreview"
        />

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <PostPreview
            :title="previewPost.getTitle().value()"
            :content="previewPost.getContent().value()"
            :published-at="previewPost.getPublishedAt()?.toISOString()"
            :scheduled-at="previewPost.getScheduledAt()?.toISOString()"
            :can-publish="previewPost.isDraft()"
            @close="closePreview"
            @edit="editPost(previewPost)"
            @publish="publishPost(previewPost)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { usePostsStore } from '../../stores/posts.store';
import PostCard from './PostCard.vue';
import PostPreview from './PostPreview.vue';
import { Post } from '../../../domain/entities/post.entity';
import type { PostFilters } from '../../../domain/types/post.types';

// Composables
const router = useRouter();
const postsStore = usePostsStore();

// Reactive state
const viewMode = ref<'grid' | 'list'>('grid');
const searchQuery = ref('');
const statusFilter = ref('');
const sortBy = ref('updated_at');
const showPreviewModal = ref(false);
const previewPost = ref<any>(null);

// Store computed
const posts = computed(() => postsStore.posts);
const pagination = computed(() => postsStore.pagination);
const isLoading = computed(() => postsStore.isLoading);
const hasError = computed(() => postsStore.hasError);
const errorMessage = computed(() => postsStore.error);

// Local computed
const totalPosts = computed(() => pagination.value?.total || 0);

const hasActiveFilters = computed(() => {
  return searchQuery.value !== '' || statusFilter.value !== '' || sortBy.value !== 'updated_at';
});

const visiblePages = computed(() => {
  if (!pagination.value) return [];

  const current = pagination.value.current_page;
  const total = pagination.value.last_page;
  const delta = 2;

  const pages: number[] = [];
  const start = Math.max(1, current - delta);
  const end = Math.min(total, current + delta);

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
});

// Debounced search
let searchTimeout: NodeJS.Timeout;
const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    applyFilters();
  }, 300);
};

// Methods
const applyFilters = async () => {
  const filters: PostFilters = {
    page: 1,
    perPage: 12,
    sortBy: sortBy.value,
    sortDirection: 'desc'
  };

  if (searchQuery.value) {
    filters.search = searchQuery.value;
  }

  if (statusFilter.value) {
    filters.status = statusFilter.value;
  }

  postsStore.setFilter(filters);
  await postsStore.applyFilters();
};

const clearFilters = () => {
  searchQuery.value = '';
  statusFilter.value = '';
  sortBy.value = 'updated_at';
  postsStore.clearFilters();
  loadPosts();
};

const goToPage = (page: number) => {
  if (!pagination.value) return;

  if (page < 1 || page > pagination.value.last_page) return;

  postsStore.setFilter({ page });
  postsStore.applyFilters();
};

const loadPosts = async () => {
  try {
    await postsStore.fetchPosts();
  } catch (error) {
    console.error('Error loading posts:', error);
  }
};

const retryLoad = () => {
  postsStore.clearError();
  loadPosts();
};

// Event handlers
const handlePreview = (post: any) => {
  previewPost.value = post;
  showPreviewModal.value = true;
};

const closePreview = () => {
  showPreviewModal.value = false;
  previewPost.value = null;
};

const editPost = (post: any) => {
  router.push(`/admin/posts/${post.getId().value()}/edit`);
};

const publishPost = async (post: any) => {
  try {
    await postsStore.changePostStatus(post.getId().value(), 'published');
    closePreview();
  } catch (error) {
    console.error('Error publishing post:', error);
  }
};

const handleStatusChange = async (post: any, status: string) => {
  // Refresh the list to show updated status
  await loadPosts();
};

const handleDelete = async (post: any) => {
  // Refresh the list after deletion
  await loadPosts();
};

// Lifecycle
onMounted(() => {
  loadPosts();
});

// Watchers
watch(viewMode, () => {
  // Save view mode preference
  localStorage.setItem('posts-view-mode', viewMode.value);
});

// Load view mode preference
const savedViewMode = localStorage.getItem('posts-view-mode') as 'grid' | 'list';
if (savedViewMode && ['grid', 'list'].includes(savedViewMode)) {
  viewMode.value = savedViewMode;
}
</script>