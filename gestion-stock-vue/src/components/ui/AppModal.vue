<script setup>
import { useModal } from '@/composables/useModal.js';
 
const { estOuvert, titre, composantActif, propsActives, fermer } = useModal();
</script>
 
<template>
  <Teleport to="body">
    <div
      v-if="estOuvert"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="fermer"
    >
      <div class="w-full max-w-lg rounded-lg bg-white shadow-xl">
        <div class="flex items-center justify-between border-b px-6 py-4">
          <h2 class="text-lg font-semibold text-gray-800">
            {{ titre }}
          </h2>
 
          <button
            type="button"
            @click="fermer"
            class="text-xl leading-none text-gray-400 hover:text-gray-600"
            aria-label="Fermer"
          >
            &times;
          </button>
        </div>
 
        <div class="p-6">
          <component
            :is="composantActif"
            v-bind="propsActives"
            @fermer="fermer"
          />
        </div>
      </div>
    </div>
  </Teleport>
</template>
