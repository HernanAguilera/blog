import { getContainer } from '~/shared/container/simple-container'
import { configureContainer } from '~/shared/container/bindings'

export default defineNuxtPlugin(() => {
  const container = getContainer()

  // Configure all service bindings
  configureContainer(container)

  // Make container available throughout the app
  return {
    provide: {
      container,
    },
  }
})
