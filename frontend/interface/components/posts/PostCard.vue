<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
    <!-- Card header -->
    <div class="p-6">
      <!-- Title and status -->
      <div class="flex items-start justify-between mb-2">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white line-clamp-2 flex-1 mr-3">
          <NuxtLink
            :to="viewUrl"
            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"
          >
            {{ post.getTitle().value() || 'Sin título' }}
          </NuxtLink>
        </h3>
        <PostStatusBadge :status="post.getStatus().value()" size="sm" />
      </div>

      <!-- Excerpt -->
      <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
        {{ post.getExcerpt(150) || 'Sin contenido' }}
      </p>

      <!-- Meta information -->
      <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-4">
        <div class="flex items-center space-x-4">
          <span class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            {{ post.getWordCount() }} palabras
          </span>

          <span v-if="publishedDate" class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ publishedDate }}
          </span>

          <span v-else-if="scheduledDate" class="flex items-center text-yellow-600 dark:text-yellow-400">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ scheduledDate }}
          </span>

          <span v-else class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            {{ createdDate }}
          </span>
        </div>

        <div v-if="readingTime" class="text-gray-400 dark:text-gray-500">
          {{ readingTime }} min lectura
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
        <!-- Primary actions -->
        <div class="flex items-center space-x-2">
          <NuxtLink
            :to="editUrl"
            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/50 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/70 transition-colors duration-200"
          >
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            Editar
          </NuxtLink>

          <button
            v-if="canPreview"
            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
            @click="handlePreview"
          >
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Vista previa
          </button>

          <NuxtLink
            v-if="canViewPublic"
            :to="publicUrl"
            target="_blank"
            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/50 rounded-md hover:bg-green-100 dark:hover:bg-green-900/70 transition-colors duration-200"
          >
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            Ver publicado
          </NuxtLink>
        </div>

        <!-- Secondary actions -->
        <div class="flex items-center space-x-1">
          <!-- Quick status change -->
          <div v-if="quickActions.length > 0" class="relative">
            <button
              class="p-1.5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
              :class="{ 'bg-gray-100 dark:bg-gray-700': showQuickActions }"
              @click="showQuickActions = !showQuickActions"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
              </svg>
            </button>

            <!-- Quick actions dropdown -->
            <div
              v-if="showQuickActions"
              class="absolute right-0 mt-1 w-36 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10"
              @click.away="showQuickActions = false"
            >
              <div class="py-1">
                <button
                  v-for="action in quickActions"
                  :key="action.status"
                  class="block w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100"
                  @click="handleQuickAction(action.status)"
                >
                  {{ action.label }}
                </button>

                <hr v-if="canDelete" class="my-1">

                <button
                  v-if="canDelete"
                  class="block w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50"
                  @click="handleDelete"
                >
                  Eliminar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { usePostsStore } from '../../stores/posts.store';
import PostStatusBadge from './PostStatusBadge.vue';
import { Post } from '../../../domain/entities/post.entity';

interface Props {
  post: any;
  showActions?: boolean;
  compact?: boolean;
}

interface Emits {
  (e: 'preview', post: any): void;
  (e: 'status-change', post: any, status: string): void;
  (e: 'delete', post: any): void;
  (e: 'action', action: string, post: any): void;
}

const props = withDefaults(defineProps<Props>(), {
  showActions: true,
  compact: false
});

const emit = defineEmits<Emits>();

// Composables
const router = useRouter();
const postsStore = usePostsStore();

// Reactive state
const showQuickActions = ref(false);

// Computed properties
const viewUrl = computed(() => `/admin/posts/${props.post.getId().value()}`);
const editUrl = computed(() => `/admin/posts/${props.post.getId().value()}/edit`);

const publicUrl = computed(() => {
  if (!props.post.isPublished()) return '';
  return props.post.getPublicUrl();
});

const canPreview = computed(() => {
  return !props.post.getContent().isEmpty();
});

const canViewPublic = computed(() => {
  return props.post.isPublished() && props.post.getPublishedAt();
});

const canDelete = computed(() => {
  return props.post.isDraft() || props.post.isArchived();
});

const publishedDate = computed(() => {
  const date = props.post.getPublishedAt();
  if (!date) return null;

  return formatDate(date);
});

const scheduledDate = computed(() => {
  const date = props.post.getScheduledAt();
  if (!date) return null;

  return `Programado para ${formatDate(date)}`;
});

const createdDate = computed(() => {
  const date = props.post.getCreatedAt();
  if (!date) return 'Fecha desconocida';

  return `Creado ${formatDate(date)}`;
});

const readingTime = computed(() => {
  const words = props.post.getWordCount();
  if (words === 0) return null;

  return Math.ceil(words / 200); // 200 words per minute average
});

const quickActions = computed(() => {
  const actions = [];
  const status = props.post.getStatus().value();

  switch (status) {
    case 'draft':
      actions.push(
        { status: 'published', label: 'Publicar' },
        { status: 'scheduled', label: 'Programar' }
      );
      break;
    case 'scheduled':
      actions.push(
        { status: 'published', label: 'Publicar ahora' },
        { status: 'draft', label: 'Volver a borrador' }
      );
      break;
    case 'published':
      actions.push(
        { status: 'draft', label: 'Volver a borrador' },
        { status: 'archived', label: 'Archivar' }
      );
      break;
    case 'archived':
      actions.push(
        { status: 'draft', label: 'Restaurar como borrador' },
        { status: 'published', label: 'Publicar' }
      );
      break;
  }

  return actions;
});

// Methods
const formatDate = (date: Date): string => {
  const now = new Date();
  const diffMs = now.getTime() - date.getTime();
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

  if (diffDays === 0) {
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    if (diffHours === 0) {
      const diffMinutes = Math.floor(diffMs / (1000 * 60));
      return diffMinutes <= 0 ? 'hace un momento' : `hace ${diffMinutes} min`;
    }
    return `hace ${diffHours} horas`;
  }

  if (diffDays === 1) return 'ayer';
  if (diffDays < 7) return `hace ${diffDays} días`;

  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const handlePreview = () => {
  emit('preview', props.post);
};

const handleQuickAction = async (status: string) => {
  showQuickActions.value = false;

  try {
    if (status === 'scheduled') {
      // TODO: Show date picker modal
      const scheduledAt = new Date();
      scheduledAt.setHours(scheduledAt.getHours() + 1);

      await postsStore.changePostStatus(
        props.post.getId().value(),
        status,
        scheduledAt.toISOString()
      );
    } else {
      await postsStore.changePostStatus(props.post.getId().value(), status);
    }

    emit('status-change', props.post, status);
  } catch (error) {
    console.error('Error changing status:', error);
  }
};

const handleDelete = async () => {
  showQuickActions.value = false;

  // TODO: Show confirmation modal
  const confirmed = confirm('¿Estás seguro de que quieres eliminar este post?');
  if (!confirmed) return;

  try {
    await postsStore.deletePost(props.post.getId().value());
    emit('delete', props.post);
  } catch (error) {
    console.error('Error deleting post:', error);
  }
};

// Click away directive would be handled by a custom directive or composable
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>