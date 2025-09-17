<template>
  <div class="post-editor bg-white shadow-sm border border-gray-200 rounded-lg">
    <!-- Toolbar -->
    <PostToolbar
      :current-status="currentPost?.getStatus().value() || 'draft'"
      :available-transitions="availableTransitions"
      :is-saving="postsStore.isSaving"
      :is-generating-preview="isGeneratingPreview"
      :has-changes="hasChanges"
      :auto-save-enabled="autoSaveEnabled"
      :has-unsaved-changes="postsStore.hasUnsavedChanges"
      :last-saved="postsStore.lastSaved"
      @save="handleSave"
      @preview="handlePreview"
      @settings="handleSettings"
      @status-change="handleStatusChange"
    />

    <!-- Editor container -->
    <div class="editor-container">
      <!-- Title input -->
      <div class="px-6 pt-6 pb-2">
        <input
          v-model="title"
          @input="handleTitleChange"
          type="text"
          placeholder="Título del post..."
          class="block w-full text-3xl font-bold border-0 border-b border-transparent pb-2 placeholder-gray-400 focus:border-gray-300 focus:ring-0 focus:outline-none"
        />
      </div>

      <!-- Quill editor -->
      <div class="px-6 pb-6">
        <div
          ref="editorContainer"
          class="min-h-[400px] focus:outline-none"
        />
      </div>
    </div>

    <!-- Preview modal -->
    <div
      v-if="showPreview"
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
            :title="title"
            :content="content"
            :published-at="currentPost?.getPublishedAt()?.toISOString()"
            :scheduled-at="currentPost?.getScheduledAt()?.toISOString()"
            :can-publish="canPublish"
            @close="closePreview"
            @edit="closePreview"
            @publish="handlePublish"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, computed, watch, nextTick } from 'vue';
import { usePostsStore } from '../../stores/posts.store';
import { useAuthStore } from '../../stores/auth.store';
import PostToolbar from './PostToolbar.vue';
import PostPreview from './PostPreview.vue';
import type { Post } from '../../../domain/entities/post.entity';

interface Props {
  postId?: string;
  initialTitle?: string;
  initialContent?: string;
  autoSave?: boolean;
}

interface Emits {
  (e: 'save', data: { title: string; content: string }): void;
  (e: 'change', data: { title: string; content: string }): void;
  (e: 'status-change', status: string): void;
}

const props = withDefaults(defineProps<Props>(), {
  autoSave: true
});

const emit = defineEmits<Emits>();

// Stores
const postsStore = usePostsStore();
const authStore = useAuthStore();

// Reactive state
const editorContainer = ref<HTMLElement>();
const quillInstance = ref<any>();
const title = ref(props.initialTitle || '');
const content = ref(props.initialContent || '');
const showPreview = ref(false);
const isGeneratingPreview = ref(false);
const availableTransitions = ref<string[]>([]);

// Auto-save configuration
const autoSaveEnabled = ref(props.autoSave);

// Computed properties
const currentPost = computed(() => postsStore.currentPost);
const hasChanges = computed(() => {
  const post = currentPost.value;
  if (!post) return title.value !== '' || content.value !== '';

  return (
    title.value !== post.getTitle().value() ||
    content.value !== post.getContent().value()
  );
});

const canPublish = computed(() => {
  return title.value.trim() !== '' && content.value.trim() !== '';
});

// Quill configuration
const quillConfig = {
  theme: 'snow',
  placeholder: 'Escribe tu contenido aquí...',
  modules: {
    toolbar: [
      [{ 'header': [1, 2, 3, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      ['blockquote', 'code-block'],
      [{ 'list': 'ordered'}, { 'list': 'bullet' }],
      [{ 'indent': '-1'}, { 'indent': '+1' }],
      ['link', 'image'],
      [{ 'align': [] }],
      ['clean']
    ]
  }
};

// Initialize Quill editor
const initializeEditor = async () => {
  await nextTick();

  if (!editorContainer.value) {
    console.error('Editor container not found');
    return;
  }

  // Dynamic import of Quill to avoid SSR issues
  const { default: Quill } = await import('quill');
  await import('quill/dist/quill.snow.css');

  quillInstance.value = new Quill(editorContainer.value, quillConfig);

  // Set initial content
  if (content.value) {
    quillInstance.value.root.innerHTML = content.value;
  }

  // Listen for content changes
  quillInstance.value.on('text-change', handleContentChange);

  // Focus editor
  quillInstance.value.focus();
};

// Event handlers
const handleTitleChange = () => {
  if (autoSaveEnabled.value) {
    postsStore.markAsChanged();
  }
  emit('change', { title: title.value, content: content.value });
};

const handleContentChange = () => {
  if (!quillInstance.value) return;

  content.value = quillInstance.value.root.innerHTML;

  if (autoSaveEnabled.value) {
    postsStore.markAsChanged();
  }

  emit('change', { title: title.value, content: content.value });
};

const handleSave = async () => {
  const data = { title: title.value, content: content.value };

  try {
    if (props.postId) {
      await postsStore.updatePost(props.postId, data);
    } else {
      const newPost = await postsStore.createPost({
        ...data,
        status: 'draft'
      });

      if (newPost) {
        // Update URL or emit post created event
        emit('save', data);
      }
    }
  } catch (error) {
    console.error('Error saving post:', error);
  }
};

const handlePreview = async () => {
  showPreview.value = true;

  // Generate preview if post exists
  if (props.postId) {
    try {
      isGeneratingPreview.value = true;
      // Could generate server-side preview here
      await new Promise(resolve => setTimeout(resolve, 500)); // Simulate API call
    } catch (error) {
      console.error('Error generating preview:', error);
    } finally {
      isGeneratingPreview.value = false;
    }
  }
};

const closePreview = () => {
  showPreview.value = false;
};

const handlePublish = async () => {
  if (!props.postId) return;

  try {
    await postsStore.changePostStatus(props.postId, 'published');
    closePreview();
  } catch (error) {
    console.error('Error publishing post:', error);
  }
};

const handleStatusChange = async (status: string) => {
  if (!props.postId) return;

  try {
    if (status === 'scheduled') {
      // TODO: Show date picker modal for scheduling
      const scheduledAt = new Date();
      scheduledAt.setHours(scheduledAt.getHours() + 1); // Default to 1 hour from now

      await postsStore.changePostStatus(props.postId, status, scheduledAt.toISOString());
    } else {
      await postsStore.changePostStatus(props.postId, status);
    }

    emit('status-change', status);
  } catch (error) {
    console.error('Error changing status:', error);
  }
};

const handleSettings = () => {
  // TODO: Show settings modal
  console.log('Settings clicked');
};

// Load post transitions
const loadTransitions = async () => {
  if (!props.postId) return;

  try {
    const transitions = await postsStore.getPostTransitions(props.postId);
    if (transitions) {
      availableTransitions.value = transitions.available_transitions;
    }
  } catch (error) {
    console.error('Error loading transitions:', error);
  }
};

// Auto-save functionality
const setupAutoSave = () => {
  if (autoSaveEnabled.value) {
    postsStore.enableAutoSave({ enabled: true, interval: 30000 }); // 30 seconds
  }
};

// Load existing post if postId is provided
const loadPost = async () => {
  if (!props.postId) return;

  try {
    const post = await postsStore.fetchPost(props.postId);
    if (post) {
      title.value = post.getTitle().value();
      content.value = post.getContent().value();

      // Update Quill content
      if (quillInstance.value) {
        quillInstance.value.root.innerHTML = content.value;
      }
    }
  } catch (error) {
    console.error('Error loading post:', error);
  }
};

// Lifecycle hooks
onMounted(async () => {
  await initializeEditor();

  if (props.postId) {
    await loadPost();
    await loadTransitions();
  }

  setupAutoSave();
});

onBeforeUnmount(() => {
  postsStore.disableAutoSave();
  postsStore.cleanup();
});

// Watchers
watch(() => props.postId, async (newPostId) => {
  if (newPostId) {
    await loadPost();
    await loadTransitions();
  }
});

// Expose methods for parent components
defineExpose({
  save: handleSave,
  getContent: () => ({ title: title.value, content: content.value }),
  setContent: (newTitle: string, newContent: string) => {
    title.value = newTitle;
    content.value = newContent;

    if (quillInstance.value) {
      quillInstance.value.root.innerHTML = newContent;
    }
  }
});
</script>

<style scoped>
.post-editor {
  @apply max-w-none;
}

.editor-container {
  @apply bg-white;
}

/* Customize Quill editor styles */
:deep(.ql-editor) {
  @apply text-base leading-relaxed;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  min-height: 400px;
  padding: 0;
}

:deep(.ql-editor.ql-blank::before) {
  @apply text-gray-400 italic;
  left: 0;
}

:deep(.ql-toolbar) {
  @apply border-0 border-b border-gray-200 bg-gray-50;
}

:deep(.ql-container) {
  @apply border-0;
}

/* Custom focus styles */
:deep(.ql-editor:focus) {
  @apply outline-none;
}

/* Title input custom styles */
input[type="text"] {
  background: transparent;
}

input[type="text"]:focus {
  @apply border-gray-300;
}
</style>