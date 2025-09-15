<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <NuxtLink to="/" class="text-2xl font-bold text-gray-900">BlogV2</NuxtLink>
          <nav class="space-x-4">
            <NuxtLink to="/" class="text-gray-600 hover:text-gray-900">Home</NuxtLink>
            <NuxtLink to="/auth/login" class="text-gray-600 hover:text-gray-900">Login</NuxtLink>
            <NuxtLink to="/auth/register" class="text-gray-600 hover:text-gray-900">Register</NuxtLink>
          </nav>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">
          Bienvenido a BlogV2
        </h2>
        <p class="text-lg text-gray-600 mb-8">
          Plataforma de blogging con arquitectura limpia
        </p>
        <div class="space-x-4">
          <NuxtLink to="/auth/login" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
            Iniciar Sesión
          </NuxtLink>
          <NuxtLink to="/auth/register" class="inline-block bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">
            Registrarse
          </NuxtLink>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
// Head configuration
useHead({
  title: 'BlogV2 - Plataforma de Blogging',
  meta: [
    {
      name: 'description',
      content: 'BlogV2 es una plataforma de blogging moderna construida con arquitectura limpia.'
    }
  ]
});
</script>