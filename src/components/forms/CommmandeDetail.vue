<script setup>
import AppButton from '@/components/ui/AppButton.vue';

const props = defineProps({
  commande: { type: Object, required: true },
});
defineEmits(['fermer']);

function libelleProduit(produitId, produits) {
  return produits.find((p) => p.id === produitId)?.libelle ?? `Produit #${produitId}`;
}
</script>

<template>
  <div class="space-y-4">
    <p class="text-sm text-gray-500">
      Passée le {{ new Date(commande.creeLe).toLocaleString('fr-FR') }}
    </p>

    <div class="rounded-lg border">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-gray-500">Produit</th>
            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-gray-500">Qté</th>
            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-gray-500">Prix payé</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="ligne in commande.lignes" :key="ligne.id">
            <td class="px-4 py-2 text-sm text-gray-700">Produit #{{ ligne.produitId }}</td>
            <td class="px-4 py-2 text-sm text-gray-700">{{ ligne.quantite }}</td>
            <td class="px-4 py-2 text-sm text-gray-700">{{ ligne.prixUnitaire }} FCFA</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex items-center justify-between border-t pt-3">
      <span class="text-sm font-semibold text-gray-800">Total : {{ commande.total }} FCFA</span>
      <AppButton type="button" variant="secondary" @click="$emit('fermer')">
        Fermer
      </AppButton>
    </div>
  </div>
</template>