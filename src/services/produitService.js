import { apiClient } from './apiClient.js';

export function listerProduits() {
  return apiClient.get('/produits');
}

export function trouverProduit(id) {
  return apiClient.get(`/produits/${id}`);
}

export function creerProduit({ libelle, prixUnitaire, categorieId, photo }) {
  const formData = new FormData();
  formData.append('libelle', libelle);
  formData.append('prixUnitaire', prixUnitaire);
  formData.append('categorieId', categorieId);
  if (photo) {
    formData.append('photo', photo);
  }
  return apiClient.post('/produits', formData);
}

export function modifierProduit(id, donnees) {
  return apiClient.patch(`/produits/${id}`, donnees);
}

export function remplacerPhoto(id, photo) {
  const formData = new FormData();
  formData.append('photo', photo);
  return apiClient.post(`/produits/${id}/photo`, formData);
}

export function supprimerProduit(id) {
  return apiClient.delete(`/produits/${id}`);
}