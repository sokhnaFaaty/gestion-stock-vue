<script setup>
import { onMounted, ref } from 'vue';
import * as categorieService from '@/services/categorieService.js';
import { useModal } from '@/composables/useModal.js';
import { useConfirm } from '@/composables/useConfirm.js';
import { useToast } from '@/composables/useToast.js';
import AppTable from '@/components/ui/AppTable.vue';
import AppButton from '@/components/ui/AppButton.vue';
import CategorieForm from '@/components/forms/CategorieForm.vue';
 
const { ouvrir } = useModal();
const { demanderConfirmation } = useConfirm();
const { succes, erreur: toastErreur } = useToast();
 
const categories = ref([]);
const chargement = ref(false);
 
const colonnes = [
  { cle: 'id', label: 'ID' },
  { cle: 'libelle', label: 'Libellé' },
];
 
async function charger() {
  chargement.value = true;
 
  try {
    const data = await categorieService.listerCategories();
    categories.value = Array.isArray(data) ? data : [];
  } catch (e) {
    toastErreur(e.message);
  } finally {
    chargement.value = false;
  }
}
 
function ouvrirCreation() {
  ouvrir(CategorieForm, {
    titre: 'Nouvelle catégorie',
    props: {
      onSucces: charger,
    },
  });
}
 
function ouvrirModification(categorie) {
  ouvrir(CategorieForm, {
    titre: 'Modifier la catégorie',
    props: {
      categorie,
      onSucces: charger,
    },
  });
}
 
async function confirmerSuppression(categorie) {
  const ok = await demanderConfirmation(
    `Supprimer la catégorie « ${categorie.libelle} » ?`,
  );
 
  if (!ok) {
    return;
  }
 
  try {
    await categorieService.supprimerCategorie(categorie.id);
    succes('Catégorie supprimée.');
    await charger();
  } catch (e) {
    toastErreur(e.message);
  }
}
 
onMounted(charger);
</script>
 
<template>
  <div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Catégories</h1>
        <p class="mt-1 text-sm text-gray-500">
          Créer, modifier et supprimer les catégories.
        </p>
      </div>
 
      <AppButton variant="primary" @click="ouvrirCreation">
        <template #icone>
          <i class="fa-solid fa-plus"></i>
        </template>
        Nouvelle catégorie
      </AppButton>
    </div>
 
    <p v-if="chargement" class="text-gray-500">
      Chargement...
    </p>
 
    <AppTable
      v-else
      :colonnes="colonnes"
      :lignes="categories"
      @modifier="ouvrirModification"
      @supprimer="confirmerSuppression"
    />
  </div>
</template>


