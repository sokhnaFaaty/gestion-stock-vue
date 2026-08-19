<script setup>
import { useToast } from '@/composables/useToast.js';
 
const { toasts } = useToast();
</script>
 
<template>
  <div class="fixed right-4 top-4 z-50 flex flex-col gap-2">
    <TransitionGroup name="toast">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="min-w-[240px] rounded-lg px-4 py-3 text-sm font-medium text-white shadow-lg"
        :class="toast.type === 'succes' ? 'bg-green-600' : 'bg-red-600'"
      >
        {{ toast.message }}
      </div>
    </TransitionGroup>
  </div>
</template>
 
<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.25s ease;
}
 
.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
