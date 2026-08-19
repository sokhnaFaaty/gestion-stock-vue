<script setup>
import { ref } from 'vue';
import * as categorieService from '@/services/categorieService.js';
import { useToast } from '@/composables/useToast.js';
import AppInput from '@/components/ui/AppInput.vue';
import AppButton from '@/components/ui/AppButton.vue';
 
const props = defineProps({
  categorie: { type: Object, default: null },
});
 
const emit = defineEmits(['fermer', 'succes']);
const { succes, erreur: toastErreur } = useToast();
 
const libelle = ref(props.categorie?.libelle ?? '');
const erreurLibelle = ref('');
const chargement = ref(false);
 
async function soumettre() {
  erreurLibelle.value = '';
 
  const valeur = libelle.value.trim();
 
  if (!valeur) {
    erreurLibelle.value = 'Le libellé est obligatoire.';
    return;
  }
 
  if (valeur.length < 2) {
    erreurLibelle.value = 'Le libellé doit contenir au moins 2 caractères.';
    return;
  }
 
  chargement.value = true;
 
  try {
    if (props.categorie) {
      await categorieService.modifierCategorie(
        props.categorie.id,
        valeur,
      );
      succes('Catégorie modifiée.');
    } else {
      await categorieService.creerCategorie(valeur);
      succes('Catégorie créée.');
    }
 
    emit('succes');
    emit('fermer');
  } catch (e) {
    toastErreur(e.message);
  } finally {
    chargement.value = false;
  }
}
</script>
 
<template>
  <form @submit.prevent="soumettre" class="space-y-4">
    <AppInput
      v-model="libelle"
      label="Libellé"
      placeholder="Ex. Informatique"
      :error="erreurLibelle"
      required
    />
 
    <div class="flex justify-end gap-3 pt-2">
      <AppButton
        type="button"
        variant="secondary"
        @click="$emit('fermer')"
      >
        Annuler
      </AppButton>
 
      <AppButton
        type="submit"
        variant="primary"
        :disabled="chargement"
      >
        {{ chargement ? 'Enregistrement...' : 'Enregistrer' }}
      </AppButton>
    </div>
  </form>
</template>
