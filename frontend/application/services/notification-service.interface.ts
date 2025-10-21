/**
 * Notification Service Interface
 *
 * Define el contrato para el servicio de notificaciones.
 * Permite desacoplar la aplicación de implementaciones específicas (vue-toastification, etc.)
 * siguiendo el principio de inversión de dependencias.
 */

export interface NotificationOptions {
  /**
   * Tiempo en milisegundos antes de que se cierre automáticamente
   * @default según la variante (3000-8000ms)
   */
  timeout?: number

  /**
   * Si se puede cerrar manualmente con botón
   * @default true
   */
  closeable?: boolean

  /**
   * Posición de la notificación en pantalla
   * @default 'top-right'
   */
  position?: 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left' | 'top-center' | 'bottom-center'
}

export interface NotificationServiceInterface {
  /**
   * Muestra una notificación de éxito
   * @param message Mensaje a mostrar
   * @param options Opciones adicionales
   */
  success(message: string, options?: NotificationOptions): void

  /**
   * Muestra una notificación de error
   * @param message Mensaje a mostrar
   * @param options Opciones adicionales
   */
  error(message: string, options?: NotificationOptions): void

  /**
   * Muestra una notificación de advertencia
   * @param message Mensaje a mostrar
   * @param options Opciones adicionales
   */
  warning(message: string, options?: NotificationOptions): void

  /**
   * Muestra una notificación informativa
   * @param message Mensaje a mostrar
   * @param options Opciones adicionales
   */
  info(message: string, options?: NotificationOptions): void

  /**
   * Cierra la última notificación mostrada
   */
  clear(): void

  /**
   * Cierra todas las notificaciones activas
   */
  clearAll(): void
}
