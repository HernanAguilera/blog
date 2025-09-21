<template>
  <NuxtLayout name="profile">
    <div>
      <!-- Breadcrumb -->
      <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
          <li>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
          </li>
          <li class="text-gray-900 dark:text-gray-100 font-medium">Mi perfil</li>
        </ol>
      </nav>

      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Mi perfil</h1>
        <p class="text-gray-600 dark:text-gray-400">Gestiona tu información personal</p>
      </div>

      <!-- Profile Form -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
        <form @submit.prevent="handleUpdateProfile">
          <div class="space-y-6">
            <!-- Avatar Section -->
            <div class="flex items-center space-x-6">
              <div class="w-16 h-16 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ user?.getName() || 'Usuario' }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ user?.getRole().value() || 'Rol' }}</p>
              </div>
            </div>

            <!-- Name Field -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Nombre de usuario
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                :placeholder="user?.getName() || 'usuario123'"
              />
            </div>

            <!-- Email Field -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Correo electrónico
              </label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                :placeholder="user?.getEmail().value() || 'miusuario@ejemplo.com'"
              />
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
              <button
                type="button"
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600"
                @click="resetForm"
              >
                Cancelar
              </button>
              <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 dark:hover:bg-blue-700 disabled:opacity-50"
                :disabled="loading"
              >
                {{ loading ? 'Guardando...' : 'Guardar cambios' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useAuthStore } from '../../interface/stores/auth.store';

// Composables
const authStore = useAuthStore();

// State
const loading = ref(false);
const form = reactive({
  name: '',
  email: ''
});

// Computed
const user = computed(() => authStore.currentUser);

// Methods
const handleUpdateProfile = async () => {
  loading.value = true;
  try {
    // TODO: Implement profile update API call
    console.log('Updating profile:', form);
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000));
    // Show success message
  } catch (error) {
    console.error('Error updating profile:', error);
  } finally {
    loading.value = false;
  }
};

const resetForm = () => {
  if (user.value) {
    form.name = user.value.getName();
    form.email = user.value.getEmail().value();
  }
};

// Initialize form with user data
onMounted(() => {
  resetForm();
});

// SEO
useHead({
  title: 'Mi Perfil - BlogV2',
  meta: [
    { name: 'description', content: 'Gestiona tu información personal en BlogV2' }
  ]
});
</script>