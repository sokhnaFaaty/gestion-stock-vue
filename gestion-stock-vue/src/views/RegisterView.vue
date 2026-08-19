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
 
const nom = ref('');
const email = ref('');
const motDePasse = ref('');
const chargement = ref(false);
 
async function soumettre() {
  chargement.value = true;
 
  try {
    await auth.register(
      nom.value.trim(),
      email.value.trim(),
      motDePasse.value,
    );
 
    succes('Compte créé, vous pouvez vous connecter.');
    router.replace('/login');
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
        <i class="fa-solid fa-user-plus mb-3 text-3xl text-indigo-600"></i>
        <h1 class="text-xl font-bold text-gray-800">Créer un compte</h1>
      </div>
 
      <AppInput
        v-model="nom"
        label="Nom"
        autocomplete="name"
        required
      />
 
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
        autocomplete="new-password"
        required
      />
 
      <AppButton
        type="submit"
        variant="primary"
        class="w-full"
        :disabled="chargement"
      >
        {{ chargement ? 'Création...' : 'Créer le compte' }}
      </AppButton>
 
      <p class="text-center text-sm text-gray-500">
        Déjà un compte ?
        <RouterLink to="/login" class="text-indigo-600 hover:underline">
          Se connecter
        </RouterLink>
      </p>
    </form>
  </div>
</template>
