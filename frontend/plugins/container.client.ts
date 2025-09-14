import { SimpleContainer } from '~/shared/container/simple-container';
import { configureContainer } from '~/shared/container/bindings';

export default defineNuxtPlugin(() => {
  const container = new SimpleContainer();

  // Configurar bindings
  configureContainer(container);

  return {
    provide: {
      container
    }
  };
});