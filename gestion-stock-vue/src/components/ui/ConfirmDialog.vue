<script setup>
import { useConfirm } from '@/composables/useConfirm.js';
 
const { estOuvert, message, confirmer, annuler } = useConfirm();
</script>
 
<template>
  <Teleport to="body">
    <div
      v-if="estOuvert"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="annuler"
    >
      <div class="w-full max-w-sm rounded-lg bg-white p-6 shadow-xl">
        <p class="mb-6 text-gray-800">{{ message }}</p>
 
        <div class="flex justify-end gap-3">
          <button
            type="button"
            @click="annuler"
            class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
          >
            Annuler
          </button>
 
          <button
            type="button"
            @click="confirmer"
            class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
          >
            Confirmer
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>
