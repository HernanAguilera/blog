<template>
  <div>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>

    <!-- Global Loading Overlay -->
    <LoadingOverlay />

    <!-- Global Modal Container -->
    <ModalContainer />
  </div>
</template>

<script setup>
import LoadingOverlay from '~/interface/components/ui/LoadingOverlay.vue';
import ModalContainer from '~/interface/components/ui/ModalContainer.vue';
</script>

<style>
@import "~/assets/css/main.css";
</style>