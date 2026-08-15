let taches = [];
let prochainId = 1;
 
export function creerTache(titre) {
  const nouvelleTache = {
    id: prochainId++,
    titre: titre.trim(),
    statut: 'active',
    supprime: false,
  };
 
  taches = [...taches, nouvelleTache];
  return nouvelleTache;
}
 
export function listerTaches() {
  return taches.filter((tache) => !tache.supprime);
}
 
export function listerCorbeille() {
  return taches.filter((tache) => tache.supprime);
}
 
export function trouverTache(id, { dansCorbeille = false } = {}) {
  return taches.find(
    (tache) =>
    tache.id === id && tache.supprime === dansCorbeille
  );
}
 
export function terminerTache(id) {
  taches = taches.map((tache) =>
    tache.id === id
    ? { ...tache, statut: 'terminee' }
    : tache
  );
 
  return trouverTache(id);
}
 
export function supprimerTache(id) {
  taches = taches.map((tache) =>
    tache.id === id
    ? { ...tache, supprime: true }
    : tache
  );
 
  return trouverTache(id, { dansCorbeille: true });
}
 
export function restaurerTache(id) {
  taches = taches.map((tache) =>
    tache.id === id
    ? { ...tache, supprime: false }
    : tache
  );
 
  return trouverTache(id);
}


export function titreExiste(titre){
  return taches.some(
    (tache)=>tache.titre.toLowerCase() === titre.toLowerCase() && !tache.supprime
  );
}