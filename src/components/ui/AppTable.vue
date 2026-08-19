src/components/ui/AppTable.vue 
<script setup>
import { computed, ref, watch } from 'vue';
 
const props = defineProps({
  colonnes: { type: Array, required: true },
  lignes: { type: Array, required: true },
  parPage: { type: Number, default: 5 },
  modifierIcone: { type: String, default: 'fa-solid fa-pen' },
  modifierTitre: { type: String, default: 'Modifier' },
});
 
defineEmits(['modifier', 'supprimer']);
 
const pageActuelle = ref(1);
 
const totalPages = computed(() =>
  Math.max(1, Math.ceil(props.lignes.length / props.parPage)),
);
 
const lignesPage = computed(() => {
  const debut = (pageActuelle.value - 1) * props.parPage;
  return props.lignes.slice(debut, debut + props.parPage);
});
 
watch(
  () => props.lignes.length,
  () => {
    if (pageActuelle.value > totalPages.value) {
      pageActuelle.value = totalPages.value;
    }
  },
);
 
function pagePrecedente() {
  if (pageActuelle.value > 1) {
    pageActuelle.value--;
  }
}
 
function pageSuivante() {
  if (pageActuelle.value < totalPages.value) {
    pageActuelle.value++;
  }
}
</script>
 
<template>
  <div class="overflow-hidden rounded-lg bg-white shadow">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="col in colonnes"
              :key="col.cle"
              class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500"
            >
              {{ col.label }}
            </th>
            <th class="w-28 px-4 py-3"></th>
          </tr>
        </thead>
 
        <tbody class="divide-y divide-gray-100">
          <tr
            v-for="ligne in lignesPage"
            :key="ligne.id"
            class="hover:bg-gray-50"
          >
            <td
              v-for="col in colonnes"
              :key="col.cle"
              class="px-4 py-3 text-sm text-gray-700"
            >
              {{ ligne[col.cle] }}
            </td>
 
            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
              <button
                type="button"
                @click="$emit('modifier', ligne)"
                class="mr-3 text-indigo-600 hover:text-indigo-800"
                title="Modifier"
              >
                <i class="fa-solid fa-pen"></i>
              </button>
              <button
  type="button"
  @click="$emit('modifier', ligne)"
  class="mr-3 text-indigo-600 hover:text-indigo-800"
  :title="modifierTitre"
>
  <i :class="modifierIcone"></i>
</button>
 
              <button
                type="button"
                @click="$emit('supprimer', ligne)"
                class="text-red-600 hover:text-red-800"
                title="Supprimer"
              >
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>
 
          <tr v-if="lignes.length === 0">
            <td
              :colspan="colonnes.length + 1"
              class="px-4 py-8 text-center text-sm text-gray-400"
            >
              Aucune donnée
            </td>
          </tr>
        </tbody>
      </table>
    </div>
 
    <div
      v-if="lignes.length > 0"
      class="flex items-center justify-between border-t bg-gray-50 px-4 py-3 text-sm"
    >
      <span class="text-gray-500">
        {{ lignes.length }} élément{{ lignes.length > 1 ? 's' : '' }} —
        page {{ pageActuelle }} / {{ totalPages }}
      </span>
 
      <div class="flex items-center gap-2">
        <button
          type="button"
          @click="pagePrecedente"
          :disabled="pageActuelle === 1"
          class="rounded-md px-3 py-1.5 text-gray-600 hover:bg-gray-200 disabled:opacity-40"
        >
          Précédent
        </button>
 
        <button
          type="button"
          @click="pageSuivante"
          :disabled="pageActuelle === totalPages"
          class="rounded-md px-3 py-1.5 text-gray-600 hover:bg-gray-200 disabled:opacity-40"
        >
          Suivant
        </button>
      </div>
    </div>
  </div>
</template>


