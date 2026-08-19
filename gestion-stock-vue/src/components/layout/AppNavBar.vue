<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.js';
 
const router = useRouter();
const auth = useAuthStore();
 
const dropdownOuvert = ref(false);
const dropdownRef = ref(null);
 
function toggleDropdown() {
  dropdownOuvert.value = !dropdownOuvert.value;
}
 
function fermerDropdown() {
  dropdownOuvert.value = false;
}
 
function gererClicExterieur(event) {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    fermerDropdown();
  }
}
 
onMounted(() => {
  document.addEventListener('click', gererClicExterieur);
});
 
onUnmounted(() => {
  document.removeEventListener('click', gererClicExterieur);
});
 
function deconnexion() {
  fermerDropdown();
  auth.logout();
  router.replace('/login');
}
</script>
 
<template>
  <header class="flex h-16 shrink-0 items-center justify-between border-b bg-white px-4 md:px-6">
    <RouterLink
      to="/categories"
      class="flex items-center gap-2 font-bold text-gray-800 md:hidden"
    >
      <i class="fa-solid fa-tags text-indigo-600"></i>
      Catégories
    </RouterLink>
 
    <div class="hidden md:block"></div>
 
    <div ref="dropdownRef" class="relative">
      <button
        type="button"
        @click="toggleDropdown"
        class="flex items-center gap-2 rounded-md px-2 py-1.5 transition-colors hover:bg-gray-100"
        aria-haspopup="menu"
        :aria-expanded="dropdownOuvert"
      >
        <i class="fa-solid fa-circle-user text-2xl text-gray-400"></i>
 
        <span class="hidden text-sm font-medium text-gray-700 sm:inline">
          {{ auth.utilisateur?.nom || 'Utilisateur' }}
        </span>
 
        <i
          class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform"
          :class="{ 'rotate-180': dropdownOuvert }"
        ></i>
      </button>
 
      <div
        v-if="dropdownOuvert"
        class="absolute right-0 z-50 mt-2 w-56 rounded-lg border bg-white py-1 shadow-lg"
        role="menu"
      >
        <div class="border-b px-4 py-3">
          <p class="text-sm font-semibold text-gray-800">
            {{ auth.utilisateur?.nom || 'Utilisateur' }}
          </p>
          <p class="truncate text-xs text-gray-500">
            {{ auth.utilisateur?.email || '—' }}
          </p>
        </div>
 
        <button
          type="button"
          @click="deconnexion"
          class="flex w-full items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50"
        >
          <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
          Déconnexion
        </button>
      </div>
    </div>
  </header>
</template>
