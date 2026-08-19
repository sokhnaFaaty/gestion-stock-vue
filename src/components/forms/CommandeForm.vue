<script setup>
import { computed, ref } from 'vue';
import * as commandeService from '@/services/commandeService.js';
import { useToast } from '@/composables/useToast.js';
import AppButton from '@/components/ui/AppButton.vue';

const props = defineProps({
  produits: { type: Array, default: () => [] },
});
const emit = defineEmits(['fermer', 'succes']);
const { succes, erreur: toastErreur } = useToast();

const produitSelectionne = ref(props.produits[0]?.id ?? null);
const quantiteSaisie = ref(1);
const panier = ref([]);
const erreurAjout = ref('');
const chargement = ref(false);

function trouverProduit(id) {
  return props.produits.find((p) => p.id === id);
}

const total = computed(() =>
  panier.value.reduce(
    (somme, ligne) => somme + Number(ligne.prixUnitaire) * ligne.quantite,
    0,
  ),
);

function ajouterAuPanier() {
  erreurAjout.value = '';
  const produit = trouverProduit(produitSelectionne.value);
  const quantite = Number(quantiteSaisie.value);

  if (!produit) {
    erreurAjout.value = 'Choisissez un produit.';
    return;
  }
  if (!Number.isInteger(quantite) || quantite < 1) {
    erreurAjout.value = 'La quantité doit être un entier positif.';
    return;
  }

  const ligneExistante = panier.value.find((l) => l.produitId === produit.id);
  if (ligneExistante) {
    ligneExistante.quantite += quantite;
  } else {
    panier.value.push({
      produitId: produit.id,
      libelle: produit.libelle,
      prixUnitaire: produit.prixUnitaire,
      quantite,
    });
  }
  quantiteSaisie.value = 1;
}

function retirerLigne(produitId) {
  panier.value = panier.value.filter((l) => l.produitId !== produitId);
}

async function soumettre() {
  if (panier.value.length === 0) {
    toastErreur('Ajoutez au moins un produit au panier.');
    return;
  }

  chargement.value = true;
  try {
    await commandeService.creerCommande(
      panier.value.map((l) => ({ produitId: l.produitId, quantite: l.quantite })),
    );
    succes('Commande créée.');
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
  <div class="space-y-4">
    <div class="flex items-end gap-3">
      <div class="flex-1">
        <label class="mb-1 block text-sm font-medium text-gray-700">Produit</label>
        <select
          v-model="produitSelectionne"
          class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          <option v-for="p in produits" :key="p.id" :value="p.id">
            {{ p.libelle }} — {{ p.prixUnitaire }} FCFA
          </option>
        </select>
      </div>
      <div class="w-24">
        <label class="mb-1 block text-sm font-medium text-gray-700">Qté</label>
        <input
          v-model.number="quantiteSaisie"
          type="number"
          min="1"
          class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
        />
      </div>
      <AppButton type="button" variant="secondary" @click="ajouterAuPanier">
        Ajouter
      </AppButton>
    </div>
    <p v-if="erreurAjout" class="text-xs text-red-600">{{ erreurAjout }}</p>

    <div class="rounded-lg border">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-gray-500">Produit</th>
            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-gray-500">Qté</th>
            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-gray-500">Sous-total</th>
            <th class="w-10 px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="ligne in panier" :key="ligne.produitId">
            <td class="px-4 py-2 text-sm text-gray-700">{{ ligne.libelle }}</td>
            <td class="px-4 py-2 text-sm text-gray-700">{{ ligne.quantite }}</td>
            <td class="px-4 py-2 text-sm text-gray-700">
              {{ Number(ligne.prixUnitaire) * ligne.quantite }} FCFA
            </td>
            <td class="px-4 py-2 text-right">
              <button type="button" @click="retirerLigne(ligne.produitId)" class="text-red-600 hover:text-red-800">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>
          <tr v-if="panier.length === 0">
            <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-400">
              Panier vide
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex items-center justify-between border-t pt-3">
      <span class="text-sm font-semibold text-gray-800">Total : {{ total }} FCFA</span>
      <div class="flex gap-3">
        <AppButton type="button" variant="secondary" @click="$emit('fermer')">
          Annuler
        </AppButton>
        <AppButton type="button" variant="primary" :disabled="chargement" @click="soumettre">
          {{ chargement ? 'Validation...' : 'Valider la commande' }}
        </AppButton>
      </div>
    </div>
  </div>
</template>