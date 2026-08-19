<script setup>
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.js';
import { useToast } from '@/composables/useToast.js';
import AppInput from '@/components/ui/AppInput.vue';
import AppButton from '@/components/ui/AppButton.vue';
 
const router = useRouter();
const auth = useAuthStore();
const { erreur: toastErreur, succes } = useToast();
 
const email = ref('');
const motDePasse = ref('');
const chargement = ref(false);
 
async function soumettre() {
  chargement.value = true;
 
  try {
    await auth.login(email.value.trim(), motDePasse.value);
    succes('Connexion réussie.');
    router.replace('/categories');
  } catch (e) {
    toastErreur(e.message);
  } finally {
    chargement.value = false;
  }
}
</script>
 
<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-50 p-4">
    <form
      @submit.prevent="soumettre"
      class="w-full max-w-sm space-y-4 rounded-lg bg-white p-8 shadow"
    >
      <div class="text-center">
        <i class="fa-solid fa-lock mb-3 text-3xl text-indigo-600"></i>
        <h1 class="text-xl font-bold text-gray-800">Connexion</h1>
        <p class="mt-1 text-sm text-gray-500">
          Accédez à la gestion des catégories.
        </p>
      </div>
 
      <AppInput
        v-model="email"
        type="email"
        label="Email"
        autocomplete="email"
        required
      />
 
      <AppInput
        v-model="motDePasse"
        type="password"
        label="Mot de passe"
        autocomplete="current-password"
        required
      />
 
      <AppButton
        type="submit"
        variant="primary"
        class="w-full"
        :disabled="chargement"
      >
        {{ chargement ? 'Connexion...' : 'Se connecter' }}
      </AppButton>
 
      <p class="text-center text-sm text-gray-500">
        Pas encore de compte ?
        <RouterLink to="/register" class="text-indigo-600 hover:underline">
          Créer un compte
        </RouterLink>
      </p>
    </form>
  </div>
</template>
