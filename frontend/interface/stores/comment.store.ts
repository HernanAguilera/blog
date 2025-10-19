/**
 * Comment Store (Pinia)
 *
 * State management centralizado para comentarios
 */

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Comment } from '../../domain/entities/comment.entity';
import type { CommentTreeNode } from '../../domain/types/comment.types';

export const useCommentStore = defineStore('comment', () => {
  // State
  const commentsByPost = ref<Record<string, CommentTreeNode[]>>({});
  const pendingComments = ref<Comment[]>([]);
  const loading = ref<boolean>(false);
  const error = ref<string | null>(null);

  // Getters
  const getCommentsByPost = computed(() => {
    return (postSlug: string): CommentTreeNode[] => {
      return commentsByPost.value[postSlug] || [];
    };
  });

  const pendingCount = computed(() => pendingComments.value.length);

  const hasComments = computed(() => {
    return (postSlug: string): boolean => {
      const comments = commentsByPost.value[postSlug];
      return comments !== undefined && comments.length > 0;
    };
  });

  // Actions
  function setCommentsForPost(postSlug: string, comments: CommentTreeNode[]) {
    commentsByPost.value[postSlug] = comments;
  }

  function addCommentToPost(postSlug: string, comment: CommentTreeNode) {
    if (!commentsByPost.value[postSlug]) {
      commentsByPost.value[postSlug] = [];
    }
    commentsByPost.value[postSlug].push(comment);
  }

  function setPendingComments(comments: Comment[]) {
    pendingComments.value = comments;
  }

  function removePendingComment(commentId: string) {
    pendingComments.value = pendingComments.value.filter(
      (c) => c.getIdValue() !== commentId
    );
  }

  function setLoading(value: boolean) {
    loading.value = value;
  }

  function setError(value: string | null) {
    error.value = value;
  }

  function clearCommentsForPost(postSlug: string) {
    const { [postSlug]: _, ...rest } = commentsByPost.value;
    commentsByPost.value = rest;
  }

  function clearAll() {
    commentsByPost.value = {};
    pendingComments.value = [];
    loading.value = false;
    error.value = null;
  }

  return {
    // State
    commentsByPost,
    pendingComments,
    loading,
    error,

    // Getters
    getCommentsByPost,
    pendingCount,
    hasComments,

    // Actions
    setCommentsForPost,
    addCommentToPost,
    setPendingComments,
    removePendingComment,
    setLoading,
    setError,
    clearCommentsForPost,
    clearAll,
  };
});
