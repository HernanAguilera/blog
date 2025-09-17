import { ToastInterface } from 'vue-toastification'

declare module '#app' {
  interface NuxtApp {
    $toast: ToastInterface
  }
}

declare module 'vue' {
  interface ComponentCustomProperties {
    $toast: ToastInterface
  }
}