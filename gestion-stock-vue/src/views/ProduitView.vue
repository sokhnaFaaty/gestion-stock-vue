<script setup>
import { computed, onMounted, ref } from 'vue';
import * as produitService from '@/services/produitService.js';
import * as categorieService from '@/services/categorieService.js';
import { useModal } from '@/composables/useModal.js';
import { useConfirm } from '@/composables/useConfirm.js';
import { useToast } from '@/composables/useToast.js';
import AppTable from '@/components/ui/AppTable.vue';
import AppButton from '@/components/ui/AppButton.vue';
import ProduitForm from '@/components/forms/ProduitForm.vue';

const { ouvrir } = useModal();
const { demanderConfirmation } = useConfirm();
const { succes, erreur: toastErreur } = useToast();

const produits = ref([]);
const categories = ref([]);
const chargement = ref(false);

const colonnes = [
  { cle: 'id', label: 'ID' },
  { cle: 'libelle', label: 'Libellé' },
  { cle: 'prixAffiche', label: 'Prix' },
  { cle: 'categorieLibelle', label: 'Catégorie' },
];

function libelleCategorie(categorieId) {
  return categories.value.find((c) => c.id === categorieId)?.libelle ?? '—';
}

const lignesAffichage = computed(() =>
  produits.value.map((p) => ({
    ...p,
    prixAffiche: `${p.prixUnitaire} FCFA`,
    categorieLibelle: libelleCategorie(p.categorieId),
  })),
);

async function charger() {
  chargement.value = true;
  try {
    const [donneesProduits, donneesCategories] = await Promise.all([
      produitService.listerProduits(),
      categorieService.listerCategories(),
    ]);
    produits.value = Array.isArray(donneesProduits) ? donneesProduits : [];
    categories.value = Array.isArray(donneesCategories) ? donneesCategories : [];
  } catch (e) {
    toastErreur(e.message);
  } finally {
    chargement.value = false;
  }
}

function ouvrirCreation() {
  ouvrir(ProduitForm, {
    titre: 'Nouveau produit',
    props: {
      categories: categories.value,
      onSucces: charger,
    },
  });
}

function ouvrirModification(ligne) {
  const produit = produits.value.find((p) => p.id === ligne.id);
  ouvrir(ProduitForm, {
    titre: 'Modifier le produit',
    props: {
      produit,
      categories: categories.value,
      onSucces: charger,
    },
  });
}

async function confirmerSuppression(ligne) {
  const ok = await demanderConfirmation(
    `Supprimer le produit « ${ligne.libelle} » ?`,
  );
  if (!ok) {
    return;
  }
  try {
    await produitService.supprimerProduit(ligne.id);
    succes('Produit supprimé.');
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
        <h1 class="text-2xl font-bold text-gray-800">Produits</h1>
        <p class="mt-1 text-sm text-gray-500">
          Créer, modifier et supprimer les produits.
        </p>
      </div>
      <AppButton variant="primary" @click="ouvrirCreation">
        <template #icone>
          <i class="fa-solid fa-plus"></i>
        </template>
        Nouveau produit
      </AppButton>
    </div>

    <p v-if="chargement" class="text-gray-500">Chargement...</p>
    <AppTable
      v-else
      :colonnes="colonnes"
      :lignes="lignesAffichage"
      @modifier="ouvrirModification"
      @supprimer="confirmerSuppression"
    />
  </div>
</template>