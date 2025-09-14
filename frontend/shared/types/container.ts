export interface ContainerInterface {
  bind<T>(key: string, factory: () => T): void;
  singleton<T>(key: string, factory: () => T): void;
  get<T>(key: string): T;
  has(key: string): boolean;
  remove(key: string): void;
  clear(): void;
}

export interface ServiceBinding<T = any> {
  factory: () => T;
  singleton: boolean;
  instance?: T;
}