<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <h1 class="text-2xl font-bold text-gray-900">BlogV2</h1>
          <nav class="space-x-4">
            <a href="#" class="text-gray-600 hover:text-gray-900">Home</a>
            <a href="#" class="text-gray-600 hover:text-gray-900">About</a>
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
          <button class="btn btn-primary">
            Comenzar
          </button>
          <button class="btn btn-secondary">
            Documentación
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<style>
@import "~/assets/css/main.css";
</style>