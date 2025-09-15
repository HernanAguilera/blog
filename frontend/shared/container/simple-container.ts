import type { ContainerInterface, ServiceBinding } from '../contracts/container';

export class SimpleContainer implements ContainerInterface {
  private bindings = new Map<string, ServiceBinding>();

  bind<T>(key: string, factory: () => T): void {
    this.bindings.set(key, {
      factory,
      singleton: false,
    });
  }

  singleton<T>(key: string, factory: () => T): void {
    this.bindings.set(key, {
      factory,
      singleton: true,
    });
  }

  get<T>(key: string): T {
    const binding = this.bindings.get(key);

    if (!binding) {
      throw new Error(`Service '${key}' not found in container`);
    }

    if (binding.singleton) {
      if (!binding.instance) {
        binding.instance = binding.factory();
      }
      return binding.instance as T;
    }

    return binding.factory() as T;
  }

  has(key: string): boolean {
    return this.bindings.has(key);
  }

  remove(key: string): void {
    this.bindings.delete(key);
  }

  clear(): void {
    this.bindings.clear();
  }
}