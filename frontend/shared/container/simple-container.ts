import type { Container } from '~/shared/types/container'

export class SimpleContainer implements Container {
  private services = new Map<string | symbol, any>()
  private singletons = new Map<string | symbol, any>()
  private factories = new Map<string | symbol, () => any>()

  bind<T>(token: string | symbol, factory: () => T): void {
    this.factories.set(token, factory)
  }

  singleton<T>(token: string | symbol, factory: () => T): void {
    this.factories.set(token, factory)
    // Mark as singleton
    this.singletons.set(token, null)
  }

  resolve<T>(token: string | symbol): T {
    // Check if it's a singleton and already instantiated
    if (this.singletons.has(token)) {
      const instance = this.singletons.get(token)
      if (instance !== null) {
        return instance
      }
    }

    // Get factory
    const factory = this.factories.get(token)
    if (!factory) {
      throw new Error(`Service not found: ${String(token)}`)
    }

    // Create instance
    const instance = factory()

    // Store singleton instance
    if (this.singletons.has(token)) {
      this.singletons.set(token, instance)
    }

    return instance
  }

  has(token: string | symbol): boolean {
    return this.factories.has(token)
  }
}

// Global container instance
let containerInstance: Container | null = null

export function getContainer(): Container {
  if (!containerInstance) {
    containerInstance = new SimpleContainer()
  }
  return containerInstance
}

export function setContainer(container: Container): void {
  containerInstance = container
}
