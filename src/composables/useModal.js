import { ref, shallowRef } from 'vue';
 
const estOuvert = ref(false);
const titre = ref('');
const composantActif = shallowRef(null);
const propsActives = ref({});
 
export function useModal() {
  function ouvrir(composant, { titre: titreModal = '', props = {} } = {}) {
    composantActif.value = composant;
    propsActives.value = props;
    titre.value = titreModal;
    estOuvert.value = true;
  }
 
  function fermer() {
    estOuvert.value = false;
    composantActif.value = null;
    propsActives.value = {};
    titre.value = '';
  }
 
  return {
    estOuvert,
    titre,
    composantActif,
    propsActives,
    ouvrir,
    fermer,
  };
}
