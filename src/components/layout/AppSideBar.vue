<script setup>
import { RouterLink, useRoute } from 'vue-router';
 
const route = useRoute();
</script>
 
<template>
  <aside class="hidden w-60 shrink-0 flex-col bg-slate-900 text-slate-200 md:flex">
    <div class="border-b border-slate-800 px-6 py-5 text-lg font-bold text-white">
      Gestion Catégories
    </div>
 
    <nav class="flex-1 px-3 py-4">
      <RouterLink
        to="/categories"
        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors"
        :class="
          route.path.startsWith('/categories')
            ? 'bg-indigo-600 text-white'
            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
        "
      >
        <i class="fa-solid fa-tags w-4 text-center"></i>
        Catégories
      </RouterLink>
      <RouterLink
  to="/commandes"
  class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors"
  :class="
    route.path.startsWith('/commandes')
      ? 'bg-indigo-600 text-white'
      : 'text-slate-300 hover:bg-slate-800 hover:text-white'
  "
>
  <i class="fa-solid fa-receipt w-4 text-center"></i>
  Commandes
</RouterLink>
    </nav>
  </aside>
</template>
