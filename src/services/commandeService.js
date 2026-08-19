import { apiClient } from './apiClient.js';

export function listerCommandes() {
  return apiClient.get('/commandes');
}

export function trouverCommande(id) {
  return apiClient.get(`/commandes/${id}`);
}

// lignes : [{ produitId, quantite }]
export function creerCommande(lignes) {
  return apiClient.post('/commandes', { lignes });
}

export function supprimerCommande(id) {
  return apiClient.delete(`/commandes/${id}`);
}