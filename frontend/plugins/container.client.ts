import container from '~/shared/container/simple-container';
import { configureContainer } from '~/shared/container/bindings';
import { useAuthStore } from '~/interface/stores/auth.store';
import { ToastNotificationService } from '~/infrastructure/services/toast-notification.service';
import type { NotificationServiceInterface } from '~/application/services/notification-service.interface';

export default defineNuxtPlugin(async (nuxtApp) => {
  // Configurar bindings en el contenedor por defecto
  configureContainer(container);

  // Registrar NotificationService con acceso a $toast
  container.singleton<NotificationServiceInterface>('NotificationService', () => {
    return new ToastNotificationService(nuxtApp.$toast as any);
  });

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