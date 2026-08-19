import { apiClient } from './apiClient.js';
 
const TOKEN_KEY = 'token';
const USER_KEY = 'user';
 
export async function inscrire(nom, email, motDePasse) {
  return apiClient.post('/auth/register', {
    nom,
    email,
    motDePasse,
  });
}
 
export async function connecter(email, motDePasse) {
  const resultat = await apiClient.post('/auth/login', {
    email,
    motDePasse,
  });
 
  if (!resultat?.token || !resultat?.user) {
    throw new Error('Réponse de connexion invalide.');
  }
 
  localStorage.setItem(TOKEN_KEY, resultat.token);
  localStorage.setItem(USER_KEY, JSON.stringify(resultat.user));
 
  return resultat;
}
 
export function deconnecter() {
  localStorage.removeItem(TOKEN_KEY);
  localStorage.removeItem(USER_KEY);
}
 
export function estConnecte() {
  return Boolean(localStorage.getItem(TOKEN_KEY));
}
 
export function utilisateurCourant() {
  const valeur = localStorage.getItem(USER_KEY);
 
  if (!valeur) {
    return null;
  }
 
  try {
    return JSON.parse(valeur);
  } catch {
    localStorage.removeItem(USER_KEY);
    return null;
  }
}
