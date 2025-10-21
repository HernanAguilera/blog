<template>
  <div class="admin-comments-page">
    <CommentModerationPanel />
  </div>
</template>

<script setup lang="ts">
import CommentModerationPanel from '../../../interface/components/admin/CommentModerationPanel.vue';

// Metadata de la página
definePageMeta({
  middleware: ['auth', 'admin'],
  layout: 'admin',
});

// SEO
useHead({
  title: 'Moderación de Comentarios - Admin',
  meta: [
    { name: 'robots', content: 'noindex,nofollow' },
  ],
});
</script>

<style scoped>
.admin-comments-page {
  min-height: 100vh;
}
</style>
