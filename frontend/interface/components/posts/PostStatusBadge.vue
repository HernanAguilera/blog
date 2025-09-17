<template>
  <span
    :class="[
      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
      statusClasses
    ]"
  >
    <span
      :class="[
        'w-2 h-2 rounded-full mr-1.5',
        dotClasses
      ]"
      aria-hidden="true"
    />
    {{ statusLabel }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  status: string;
  size?: 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md'
});

const statusLabel = computed(() => {
  switch (props.status) {
    case 'draft':
      return 'Borrador';
    case 'scheduled':
      return 'Programado';
    case 'published':
      return 'Publicado';
    case 'archived':
      return 'Archivado';
    default:
      return props.status.charAt(0).toUpperCase() + props.status.slice(1);
  }
});

const statusClasses = computed(() => {
  const baseClasses = 'inline-flex items-center rounded-full text-xs font-medium';

  const sizeClasses: Record<string, string> = {
    sm: 'px-2 py-0.5',
    md: 'px-2.5 py-0.5',
    lg: 'px-3 py-1'
  };

  const colorClasses: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-800',
    scheduled: 'bg-yellow-100 text-yellow-800',
    published: 'bg-green-100 text-green-800',
    archived: 'bg-red-100 text-red-800'
  };

  return [
    baseClasses,
    sizeClasses[props.size],
    colorClasses[props.status] || 'bg-gray-100 text-gray-800'
  ].join(' ');
});

const dotClasses = computed(() => {
  const colorClasses: Record<string, string> = {
    draft: 'bg-gray-400',
    scheduled: 'bg-yellow-400',
    published: 'bg-green-400',
    archived: 'bg-red-400'
  };

  return colorClasses[props.status] || 'bg-gray-400';
});
</script>