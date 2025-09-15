/**
 * Container composable for dependency injection
 * Provides access to the service container and handles initialization
 */
import { SimpleContainer } from '../../shared/container/simple-container';
import { configureContainer } from '../../shared/container/bindings';
import { useAuthStore } from '../stores/auth.store';

let container: SimpleContainer | null = null;

/**
 * Get or create the singleton container instance
 */
export const useContainer = () => {
  if (!container) {
    container = new SimpleContainer();
    configureContainer(container);
  }

  return container;
};

/**
 * Initialize auth store dependencies from container
 * This should be called during app initialization
 */
export const initializeAuthDependencies = () => {
  const serviceContainer = useContainer();
  const authStore = useAuthStore();

  // Get services from container
  const loginUseCase = serviceContainer.get('LoginUseCase');
  const registerUseCase = serviceContainer.get('RegisterUseCase');
  const logoutUseCase = serviceContainer.get('LogoutUseCase');
  const authService = serviceContainer.get('AuthorizationService');
  const tokenStorage = serviceContainer.get('TokenStorage');

  // Initialize auth store dependencies
  authStore.initializeDependencies(
    loginUseCase,
    registerUseCase,
    logoutUseCase,
    authService,
    tokenStorage
  );
};

/**
 * Get a service from the container
 */
export const useService = <T>(serviceName: string): T => {
  const serviceContainer = useContainer();
  return serviceContainer.get<T>(serviceName);
};