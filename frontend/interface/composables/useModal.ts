import { ref, type Ref } from 'vue'

export interface ModalOptions {
  title?: string
  message: string
  variant?: 'info' | 'success' | 'warning' | 'danger' | 'error'
  confirmText?: string
  cancelText?: string
  buttonText?: string
  size?: 'sm' | 'md' | 'lg' | 'xl'
}

interface ModalState {
  isOpen: boolean
  type: 'confirm' | 'alert' | null
  options: ModalOptions | null
  resolver: ((value: any) => void) | null
}

// Estado global reactivo (singleton)
const modalState: Ref<ModalState> = ref({
  isOpen: false,
  type: null,
  options: null,
  resolver: null
})

/**
 * Composable para gestionar modales de forma programática
 *
 * Proporciona una API basada en Promises para mostrar confirmaciones y alertas
 * sin necesidad de usar componentes declarativos en el template.
 *
 * @example
 * ```typescript
 * const modal = useModal()
 *
 * // Confirmación
 * const confirmed = await modal.confirm({
 *   title: 'Eliminar comentario',
 *   message: '¿Estás seguro de que quieres eliminar este comentario?',
 *   variant: 'danger',
 *   confirmText: 'Sí, eliminar',
 *   cancelText: 'Cancelar'
 * })
 *
 * if (confirmed) {
 *   // Usuario confirmó
 * }
 *
 * // Alerta
 * await modal.alert({
 *   title: 'Éxito',
 *   message: 'El comentario ha sido eliminado',
 *   variant: 'success'
 * })
 * ```
 */
export function useModal() {
  /**
   * Muestra un modal de confirmación con opciones Sí/No
   * @param options Opciones del modal
   * @returns Promise<boolean> - true si confirmó, false si canceló
   */
  const confirm = (options: ModalOptions): Promise<boolean> => {
    return new Promise((resolve) => {
      modalState.value = {
        isOpen: true,
        type: 'confirm',
        options,
        resolver: resolve as (value: boolean) => void
      }
    })
  }

  /**
   * Muestra un modal de alerta (solo información)
   * @param options Opciones del modal
   * @returns Promise<void> - se resuelve cuando el usuario cierra el modal
   */
  const alert = (options: Omit<ModalOptions, 'confirmText' | 'cancelText'>): Promise<void> => {
    return new Promise((resolve) => {
      modalState.value = {
        isOpen: true,
        type: 'alert',
        options: options as ModalOptions,
        resolver: resolve as () => void
      }
    })
  }

  /**
   * Maneja la confirmación del usuario
   * @internal Usado por ModalContainer
   */
  const handleConfirm = () => {
    if (modalState.value.resolver && modalState.value.type === 'confirm') {
      (modalState.value.resolver as (value: boolean) => void)(true)
    }
    close()
  }

  /**
   * Maneja la cancelación del usuario
   * @internal Usado por ModalContainer
   */
  const handleCancel = () => {
    if (modalState.value.resolver) {
      if (modalState.value.type === 'confirm') {
        (modalState.value.resolver as (value: boolean) => void)(false)
      } else {
        (modalState.value.resolver as () => void)()
      }
    }
    close()
  }

  /**
   * Cierra el modal actual
   * @internal Usado por ModalContainer
   */
  const close = () => {
    modalState.value = {
      isOpen: false,
      type: null,
      options: null,
      resolver: null
    }
  }

  return {
    // Estado (para ModalContainer)
    modalState,

    // API pública
    confirm,
    alert,

    // Handlers (para ModalContainer)
    handleConfirm,
    handleCancel,
    close
  }
}
