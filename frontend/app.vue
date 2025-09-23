<template>
  <div>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>

    <!-- Global Loading Overlay -->
    <LoadingOverlay />
  </div>
</template>

<script setup>
import LoadingOverlay from '~/interface/components/ui/LoadingOverlay.vue';
</script>

<style>
@import "~/assets/css/main.css";
</style>