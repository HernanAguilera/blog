import type { ToastInterface } from 'vue-toastification'
import type { NotificationServiceInterface, NotificationOptions } from '~/application/services/notification-service.interface'

/**
 * Toast Notification Service
 *
 * Implementación del NotificationServiceInterface usando vue-toastification.
 * Esta implementación puede ser reemplazada por otra librería sin afectar
 * el resto de la aplicación.
 */
export class ToastNotificationService implements NotificationServiceInterface {
  constructor(private readonly toast: ToastInterface) {}

  success(message: string, options?: NotificationOptions): void {
    this.toast.success(message, this.mapOptions(options))
  }

  error(message: string, options?: NotificationOptions): void {
    this.toast.error(message, this.mapOptions(options))
  }

  warning(message: string, options?: NotificationOptions): void {
    this.toast.warning(message, this.mapOptions(options))
  }

  info(message: string, options?: NotificationOptions): void {
    this.toast.info(message, this.mapOptions(options))
  }

  clear(): void {
    this.toast.clear()
  }

  clearAll(): void {
    this.toast.clear()
  }

  /**
   * Mapea las opciones de nuestra interfaz a las opciones de vue-toastification
   * @private
   */
  private mapOptions(options?: NotificationOptions): any {
    if (!options) return {}

    const mapped: any = {}

    if (options.timeout !== undefined) {
      mapped.timeout = options.timeout
    }

    if (options.closeable !== undefined) {
      mapped.closeButton = options.closeable
    }

    if (options.position !== undefined) {
      // Mapear las posiciones de nuestra interfaz a las de vue-toastification
      const positionMap: Record<string, string> = {
        'top-right': 'top-right',
        'top-left': 'top-left',
        'bottom-right': 'bottom-right',
        'bottom-left': 'bottom-left',
        'top-center': 'top-center',
        'bottom-center': 'bottom-center'
      }

      mapped.position = positionMap[options.position] as any
    }

    return mapped
  }
}
