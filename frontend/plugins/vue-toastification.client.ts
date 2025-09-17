import { defineNuxtPlugin } from '#app'
import Toast, { POSITION, TYPE, useToast } from 'vue-toastification'
import 'vue-toastification/dist/index.css'

export default defineNuxtPlugin((nuxtApp) => {
  const options = {
    position: POSITION.TOP_RIGHT,
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    icon: true,
    rtl: false,
    transition: "Vue-Toastification__bounce",
    maxToasts: 20,
    newestOnTop: true,
    toastDefaults: {
      [TYPE.ERROR]: {
        timeout: 8000,
        hideProgressBar: false,
      },
      [TYPE.SUCCESS]: {
        timeout: 3000,
        hideProgressBar: true,
      },
      [TYPE.INFO]: {
        timeout: 5000,
        hideProgressBar: false,
      },
      [TYPE.WARNING]: {
        timeout: 6000,
        hideProgressBar: false,
      }
    }
  }

  nuxtApp.vueApp.use(Toast, options)

  // Make toast available globally
  const toast = useToast()
  nuxtApp.provide('toast', toast)
})