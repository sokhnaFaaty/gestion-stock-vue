<script setup>
import { computed, onMounted, ref } from 'vue';
import * as commandeService from '@/services/commandeService.js';
import * as produitService from '@/services/produitService.js';
import { useModal } from '@/composables/useModal.js';
import { useConfirm } from '@/composables/useConfirm.js';
import { useToast } from '@/composables/useToast.js';
import AppTable from '@/components/ui/AppTable.vue';
import AppButton from '@/components/ui/AppButton.vue';
import CommandeForm from '@/components/forms/CommandeForm.vue';
import CommandeDetail from '@/components/forms/CommandeDetail.vue';

const { ouvrir } = useModal();
const { demanderConfirmation } = useConfirm();
const { succes, erreur: toastErreur } = useToast();

const commandes = ref([]);
const produits = ref([]);
const chargement = ref(false);

const colonnes = [
  { cle: 'id', label: 'ID' },
  { cle: 'dateAffichee', label: 'Date' },
  { cle: 'nombreArticles', label: 'Articles' },
  { cle: 'totalAffiche', label: 'Total' },
];

const lignesAffichage = computed(() =>
  commandes.value.map((cmd) => ({
    ...cmd,
    dateAffichee: new Date(cmd.creeLe).toLocaleString('fr-FR'),
    nombreArticles: cmd.lignes.length,
    totalAffiche: `${cmd.total} FCFA`,
  })),
);

async function charger() {
  chargement.value = true;
  try {
    const [donneesCommandes, donneesProduits] = await Promise.all([
      commandeService.listerCommandes(),
      produitService.listerProduits(),
    ]);
    commandes.value = Array.isArray(donneesCommandes) ? donneesCommandes : [];
    produits.value = Array.isArray(donneesProduits) ? donneesProduits : [];
  } catch (e) {
    toastErreur(e.message);
  } finally {
    chargement.value = false;
  }
}

function ouvrirCreation() {
  ouvrir(CommandeForm, {
    titre: 'Nouvelle commande',
    props: { produits: produits.value, onSucces: charger },
  });
}

function ouvrirDetail(ligne) {
  const commande = commandes.value.find((c) => c.id === ligne.id);
  ouvrir(CommandeDetail, {
    titre: `Commande #${commande.id}`,
    props: { commande },
  });
}

async function confirmerSuppression(ligne) {
  const ok = await demanderConfirmation(`Supprimer la commande #${ligne.id} ?`);
  if (!ok) {
    return;
  }
  try {
    await commandeService.supprimerCommande(ligne.id);
    succes('Commande supprimée.');
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
        <h1 class="text-2xl font-bold text-gray-800">Commandes</h1>
        <p class="mt-1 text-sm text-gray-500">Historique et création de commandes.</p>
      </div>
      <AppButton variant="primary" @click="ouvrirCreation">
        <template #icone>
          <i class="fa-solid fa-plus"></i>
        </template>
        Nouvelle commande
      </AppButton>
    </div>

    <p v-if="chargement" class="text-gray-500">Chargement...</p>
    <AppTable
      v-else
      :colonnes="colonnes"
      :lignes="lignesAffichage"
      modifier-titre="Voir détail"
      modifier-icone="fa-solid fa-eye"
      @modifier="ouvrirDetail"
      @supprimer="confirmerSuppression"
    />
  </div>
</template>