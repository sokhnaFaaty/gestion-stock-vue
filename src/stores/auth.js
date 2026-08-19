import { defineStore } from 'pinia';
import * as authService from '@/services/authService.js';
 
export const useAuthStore = defineStore('auth', {
  state: () => ({
    utilisateur: authService.utilisateurCourant(),
    connecte: authService.estConnecte(),
  }),
 
  actions: {
    async login(email, motDePasse) {
      const resultat = await authService.connecter(email, motDePasse);
      this.utilisateur = resultat.user;
      this.connecte = true;
    },
 
    async register(nom, email, motDePasse) {
      return authService.inscrire(nom, email, motDePasse);
    },
 
    logout() {
      authService.deconnecter();
      this.utilisateur = null;
      this.connecte = false;
    },
  },
});
