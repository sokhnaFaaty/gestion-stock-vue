import { ref } from 'vue';
 
const estOuvert = ref(false);
const message = ref('');
let resolvePromise = null;
 
function terminerConfirmation(valeur) {
  estOuvert.value = false;
  resolvePromise?.(valeur);
  resolvePromise = null;
}
 
export function useConfirm() {
  function demanderConfirmation(texte) {
    message.value = texte;
    estOuvert.value = true;
 
    return new Promise((resolve) => {
      resolvePromise = resolve;
    });
  }
 
  function confirmer() {
    terminerConfirmation(true);
  }
 
  function annuler() {
    terminerConfirmation(false);
  }
 
  return {
    estOuvert,
    message,
    demanderConfirmation,
    confirmer,
    annuler,
  };
}
