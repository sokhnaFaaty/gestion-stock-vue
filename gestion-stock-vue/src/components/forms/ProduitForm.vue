<script setup>
import { computed, ref } from 'vue';
import * as produitService from '@/services/produitService.js';
import { useToast } from '@/composables/useToast.js';
import AppInput from '@/components/ui/AppInput.vue';
import AppButton from '@/components/ui/AppButton.vue';

const props = defineProps({
  produit: { type: Object, default: null },
  categories: { type: Array, default: () => [] },
});
const emit = defineEmits(['fermer', 'succes']);
const { succes, erreur: toastErreur } = useToast();

const libelle = ref(props.produit?.libelle ?? '');
const prixUnitaire = ref(props.produit?.prixUnitaire ?? '');
const categorieId = ref(props.produit?.categorieId ?? props.categories[0]?.id ?? null);
const photoFile = ref(null);

const erreurLibelle = ref('');
const erreurPrix = ref('');
const erreurCategorie = ref('');
const chargement = ref(false);

const aperçuUrl = computed(() => {
  if (photoFile.value) {
    return URL.createObjectURL(photoFile.value);
  }
  return props.produit?.photoUrl ?? null;
});

function onFileChange(event) {
  photoFile.value = event.target.files?.[0] ?? null;
}

function valider(valeurLibelle, valeurPrix) {
  erreurLibelle.value = '';
  erreurPrix.value = '';
  erreurCategorie.value = '';

  if (!valeurLibelle) {
    erreurLibelle.value = 'Le libellé est obligatoire.';
  } else if (valeurLibelle.length < 2) {
    erreurLibelle.value = 'Le libellé doit contenir au moins 2 caractères.';
  }

  const prixNombre = Number(valeurPrix);
  if (!valeurPrix || Number.isNaN(prixNombre) || prixNombre <= 0) {
    erreurPrix.value = 'Le prix doit être un nombre positif.';
  }

  if (!categorieId.value) {
    erreurCategorie.value = 'Choisissez une catégorie.';
  }

  return !erreurLibelle.value && !erreurPrix.value && !erreurCategorie.value;
}

async function soumettre() {
  const valeurLibelle = libelle.value.trim();
  const valeurPrix = prixUnitaire.value.trim();

  if (!valider(valeurLibelle, valeurPrix)) {
    return;
  }

  chargement.value = true;
  try {
    if (props.produit) {
      await produitService.modifierProduit(props.produit.id, {
        libelle: valeurLibelle,
        prixUnitaire: valeurPrix,
        categorieId: Number(categorieId.value),
      });
      if (photoFile.value) {
        await produitService.remplacerPhoto(props.produit.id, photoFile.value);
      }
      succes('Produit modifié.');
    } else {
      await produitService.creerProduit({
        libelle: valeurLibelle,
        prixUnitaire: valeurPrix,
        categorieId: categorieId.value,
        photo: photoFile.value,
      });
      succes('Produit créé.');
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
      placeholder="Ex. Clavier mécanique"
      :error="erreurLibelle"
      required
    />

    <AppInput
      v-model="prixUnitaire"
      label="Prix unitaire"
      type="number"
      placeholder="Ex. 15000"
      :error="erreurPrix"
      required
    />

    <div>
      <label class="mb-1 block text-sm font-medium text-gray-700">Catégorie</label>
      <select
        v-model="categorieId"
        class="w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
        :class="erreurCategorie ? 'border-red-400' : 'border-gray-300'"
      >
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
          {{ cat.libelle }}
        </option>
      </select>
      <p v-if="erreurCategorie" class="mt-1 text-xs text-red-600">
        {{ erreurCategorie }}
      </p>
    </div>

    <div>
      <label class="mb-1 block text-sm font-medium text-gray-700">
        Photo {{ produit ? '(remplacer)' : '' }}
      </label>
      <input
        type="file"
        accept="image/*"
        @change="onFileChange"
        class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100"
      />
      <img
        v-if="aperçuUrl"
        :src="aperçuUrl"
        alt="Aperçu"
        class="mt-2 h-20 w-20 rounded-md border object-cover"
      />
    </div>

    <div class="flex justify-end gap-3 pt-2">
      <AppButton type="button" variant="secondary" @click="$emit('fermer')">
        Annuler
      </AppButton>
      <AppButton type="submit" variant="primary" :disabled="chargement">
        {{ chargement ? 'Enregistrement...' : 'Enregistrer' }}
      </AppButton>
    </div>
  </form>
</template>