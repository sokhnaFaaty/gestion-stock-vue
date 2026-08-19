<script setup>
import { computed } from 'vue';
import { RouterView, useRoute } from 'vue-router';
import AppSidebar from '@/components/layout/AppSidebar.vue';
import AppNavbar from '@/components/layout/AppNavbar.vue';
import ToastContainer from '@/components/ui/ToastContainer.vue';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import AppModal from '@/components/ui/AppModal.vue';
 
const route = useRoute();
 
const afficherLayout = computed(() => route.meta.requiresAuth === true);
</script>
 
<template>
  <div v-if="afficherLayout" class="fixed inset-0 flex bg-gray-50">
    <AppSidebar />
 
    <div class="flex flex-1 flex-col overflow-hidden">
      <AppNavbar />
 
      <main class="flex-1 overflow-y-auto p-4 md:p-6">
        <RouterView />
      </main>
    </div>
  </div>
 
  <RouterView v-else />
 
  <ToastContainer />
  <ConfirmDialog />
  <AppModal />
</template>


