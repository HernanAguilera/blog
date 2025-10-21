<template>
  <div>
    <!-- Confirm Modal -->
    <ConfirmModal
      v-if="modalState.type === 'confirm' && modalState.isOpen"
      :is-open="modalState.isOpen"
      :title="modalState.options?.title"
      :message="modalState.options?.message || ''"
      :variant="(modalState.options?.variant as 'info' | 'success' | 'warning' | 'danger') || 'info'"
      :confirm-text="modalState.options?.confirmText"
      :cancel-text="modalState.options?.cancelText"
      :size="modalState.options?.size"
      @confirm="handleConfirm"
      @cancel="handleCancel"
    />

    <!-- Alert Modal -->
    <AlertModal
      v-if="modalState.type === 'alert' && modalState.isOpen"
      :is-open="modalState.isOpen"
      :title="modalState.options?.title"
      :message="modalState.options?.message || ''"
      :variant="(modalState.options?.variant as 'info' | 'success' | 'warning' | 'error') || 'info'"
      :button-text="modalState.options?.buttonText"
      :size="modalState.options?.size"
      @close="handleCancel"
    />
  </div>
</template>

<script setup lang="ts">
import { useModal } from '~/interface/composables/useModal'
import ConfirmModal from './ConfirmModal.vue'
import AlertModal from './AlertModal.vue'

const { modalState, handleConfirm, handleCancel } = useModal()
</script>
