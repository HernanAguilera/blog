import type { NotificationServiceInterface } from '~/application/services/notification-service.interface'

/**
 * Composable para acceder al servicio de notificaciones
 *
 * Este composable resuelve el servicio de notificaciones desde el contenedor de
 * inyección de dependencias. Proporciona una interfaz desacoplada de la implementación
 * concreta (vue-toastification, etc.)
 *
 * @example
 * ```typescript
 * const notification = useNotification()
 *
 * notification.success('Operación exitosa')
 * notification.error('Ha ocurrido un error')
 * notification.warning('Advertencia importante')
 * notification.info('Información útil')
 *
 * // Con opciones
 * notification.success('Guardado', {
 *   timeout: 3000,
 *   position: 'top-right'
 * })
 * ```
 *
 * @returns NotificationServiceInterface
 */
export function useNotification(): NotificationServiceInterface {
  const { $container } = useNuxtApp()
  return $container.get('NotificationService') as NotificationServiceInterface
}
