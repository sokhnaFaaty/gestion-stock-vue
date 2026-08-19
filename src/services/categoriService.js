import { apiClient } from './apiClient.js';
 
export function listerCategories() {
  return apiClient.get('/categories');
}
 
export function creerCategorie(libelle) {
  return apiClient.post('/categories', { libelle });
}
 
export function modifierCategorie(id, libelle) {
  return apiClient.put(`/categories/${id}`, { libelle });
}
 
export function supprimerCategorie(id) {
  return apiClient.delete(`/categories/${id}`);
}
