import container from '~/shared/container/simple-container';
import { configureContainer } from '~/shared/container/bindings';
import { useAuthStore } from '~/interface/stores/auth.store';

export default defineNuxtPlugin(async () => {
  // Configurar bindings en el contenedor por defecto
  configureContainer(container);

  // Inicializar dependencias del auth store directamente
  try {
    const authStore = useAuthStore();
    authStore.initializeDependencies(
      container.get('LoginUseCase'),
      container.get('RegisterUseCase'),
      container.get('LogoutUseCase'),
      container.get('AuthorizationService'),
      container.get('TokenStorage')
    );

    // Restaurar sesión automáticamente al iniciar la aplicación
    await authStore.restoreSession();
  } catch (error) {
    console.warn('Error initializing auth dependencies:', error);
  }

  return {
    provide: {
      container
    }
  };
});