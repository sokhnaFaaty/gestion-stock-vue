import { createRoute, z } from '@hono/zod-openapi';
import {
  InscriptionSchema, ConnexionSchema, TokenSchema, UtilisateurSchema,
  ChangerMotDePasseSchema, MotDePasseOublieSchema, CodeResetSchema,
  ReinitialiserSchema, MessageSchema,
  CategorieSchema, CreerCategorieSchema,
  ProduitSchema, ModifierProduitSchema,
  CreerProduitFormSchema, RemplacerPhotoFormSchema,
  CreerCommandeSchema, CommandeSchema,
  IdParamSchema, ErreurSchema, ErreurValidationSchema
} from './schemas.js';


// Réponses d'erreur réutilisées partout : on les déclare une fois ici
// pour qu'elles apparaissent dans Swagger sur chaque route.
const REPONSE_400 = {
  description: 'Données invalides',
  content: { 'application/json': { schema: ErreurValidationSchema } },
};

const reponseErreur = (description) => ({
  description,
  content: { 'application/json': { schema: ErreurSchema } },
});


// ---- Auth ----


export const inscrireRoute = createRoute({
  method: 'post',
  path: '/auth/register',
  tags: ['Auth'],
  summary: 'Créer un compte',
  request: { body: { content: { 'application/json': { schema: InscriptionSchema } } } },
  responses: {
    201: { description: 'Compte créé', content: { 'application/json': { schema: UtilisateurSchema } } },
    400: REPONSE_400,
    409: reponseErreur('Email déjà utilisé'),
  },
});


export const connecterRoute = createRoute({
  method: 'post',
  path: '/auth/login',
  tags: ['Auth'],
  summary: 'Se connecter et obtenir un token',
  request: { body: { content: { 'application/json': { schema: ConnexionSchema } } } },
  responses: {
    200: { description: 'Connexion réussie', content: { 'application/json': { schema: TokenSchema } } },
    400: REPONSE_400,
    401: reponseErreur('Identifiants invalides'),
  },
});


export const deconnecterRoute = createRoute({
  method: 'post',
  path: '/auth/logout',
  tags: ['Auth'],
  summary: 'Se déconnecter',
  security: [{ Bearer: [] }],
  responses: {
    200: { description: 'Déconnecté', content: { 'application/json': { schema: MessageSchema } } },
    401: reponseErreur('Token manquant ou invalide'),
  },
});


export const changerMotDePasseRoute = createRoute({
  method: 'post',
  path: '/auth/change-password',
  tags: ['Auth'],
  summary: 'Changer son mot de passe',
  security: [{ Bearer: [] }],
  request: { body: { content: { 'application/json': { schema: ChangerMotDePasseSchema } } } },
  responses: {
    200: { description: 'Mot de passe modifié', content: { 'application/json': { schema: MessageSchema } } },
    400: REPONSE_400,
    401: reponseErreur('Ancien mot de passe incorrect'),
  },
});


export const motDePasseOublieRoute = createRoute({
  method: 'post',
  path: '/auth/forgot-password',
  tags: ['Auth'],
  summary: 'Demander un code de réinitialisation',
  request: { body: { content: { 'application/json': { schema: MotDePasseOublieSchema } } } },
  responses: {
    200: { description: 'Code généré', content: { 'application/json': { schema: CodeResetSchema } } },
    400: REPONSE_400,
    404: reponseErreur('Aucun compte avec cet email'),
  },
});


export const reinitialiserMotDePasseRoute = createRoute({
  method: 'post',
  path: '/auth/reset-password',
  tags: ['Auth'],
  summary: 'Réinitialiser le mot de passe avec le code reçu',
  request: { body: { content: { 'application/json': { schema: ReinitialiserSchema } } } },
  responses: {
    200: { description: 'Mot de passe réinitialisé', content: { 'application/json': { schema: MessageSchema } } },
    400: REPONSE_400,
    401: reponseErreur('Code invalide ou expiré'),
  },
});


// ---- Catégories (protégées) ----


export const listerCategoriesRoute = createRoute({
  method: 'get', path: '/categories', tags: ['Catégories'],
  summary: 'Liste les catégories', security: [{ Bearer: [] }],
  responses: {
    200: { description: 'Liste', content: { 'application/json': { schema: z.array(CategorieSchema) } } },
    401: reponseErreur('Token manquant ou invalide'),
  },
});


export const creerCategorieRoute = createRoute({
  method: 'post', path: '/categories', tags: ['Catégories'],
  summary: 'Créer une catégorie', security: [{ Bearer: [] }],
  request: { body: { content: { 'application/json': { schema: CreerCategorieSchema } } } },
  responses: {
    201: { description: 'Catégorie créée', content: { 'application/json': { schema: CategorieSchema } } },
    400: REPONSE_400,
    409: reponseErreur('Ce libellé existe déjà'),
  },
});


export const modifierCategorieRoute = createRoute({
  method: 'put', path: '/categories/{id}', tags: ['Catégories'],
  summary: 'Modifier une catégorie', security: [{ Bearer: [] }],
  request: {
    params: IdParamSchema,
    body: { content: { 'application/json': { schema: CreerCategorieSchema } } },
  },
  responses: {
    200: { description: 'Catégorie modifiée', content: { 'application/json': { schema: CategorieSchema } } },
    400: REPONSE_400,
    404: reponseErreur('Catégorie introuvable'),
    409: reponseErreur('Ce libellé existe déjà'),
  },
});


export const supprimerCategorieRoute = createRoute({
  method: 'delete', path: '/categories/{id}', tags: ['Catégories'],
  summary: 'Supprimer une catégorie', security: [{ Bearer: [] }],
  request: { params: IdParamSchema },
  responses: {
    200: { description: 'Catégorie supprimée', content: { 'application/json': { schema: CategorieSchema } } },
    400: REPONSE_400,
    404: reponseErreur('Catégorie introuvable'),
  },
});


// ---- Produits (protégées) ----


export const listerProduitsRoute = createRoute({
  method: 'get', path: '/produits', tags: ['Produits'],
  summary: 'Liste les produits', security: [{ Bearer: [] }],
  responses: {
    200: { description: 'Liste', content: { 'application/json': { schema: z.array(ProduitSchema) } } },
    401: reponseErreur('Token manquant ou invalide'),
  },
});


export const detailProduitRoute = createRoute({
  method: 'get', path: '/produits/{id}', tags: ['Produits'],
  summary: "Détail d'un produit", security: [{ Bearer: [] }],
  request: { params: IdParamSchema },
  responses: {
    200: { description: 'Produit trouvé', content: { 'application/json': { schema: ProduitSchema } } },
    400: REPONSE_400,
    404: reponseErreur('Produit introuvable'),
  },
});


export const creerProduitRoute = createRoute({
  method: 'post', path: '/produits', tags: ['Produits'],
  summary: 'Créer un produit (avec photo)', security: [{ Bearer: [] }],
  request: {
    body: {
      content: {
        'multipart/form-data': { schema: CreerProduitFormSchema },
      },
    },
  },
  responses: {
    201: { description: 'Produit créé', content: { 'application/json': { schema: ProduitSchema } } },
    400: REPONSE_400,
    409: reponseErreur('Ce libellé existe déjà'),
  },
});


export const modifierProduitRoute = createRoute({
  method: 'patch', path: '/produits/{id}', tags: ['Produits'],
  summary: 'Modifier libellé / prix / catégorie', security: [{ Bearer: [] }],
  request: {
    params: IdParamSchema,
    body: { content: { 'application/json': { schema: ModifierProduitSchema } } },
  },
  responses: {
    200: { description: 'Produit modifié', content: { 'application/json': { schema: ProduitSchema } } },
    400: REPONSE_400,
    404: reponseErreur('Produit introuvable'),
    409: reponseErreur('Ce libellé existe déjà'),
  },
});


export const remplacerPhotoRoute = createRoute({
  method: 'post', path: '/produits/{id}/photo', tags: ['Produits'],
  summary: "Remplacer la photo d'un produit", security: [{ Bearer: [] }],
  request: {
    params: IdParamSchema,
    body: {
      content: {
        'multipart/form-data': { schema: RemplacerPhotoFormSchema },
      },
    },
  },
  responses: {
    200: { description: 'Photo mise à jour', content: { 'application/json': { schema: ProduitSchema } } },
    400: REPONSE_400,
    404: reponseErreur('Produit introuvable'),
  },
});


export const supprimerProduitRoute = createRoute({
  method: 'delete', path: '/produits/{id}', tags: ['Produits'],
  summary: 'Supprimer un produit', security: [{ Bearer: [] }],
  request: { params: IdParamSchema },
  responses: {
    200: { description: 'Produit supprimé', content: { 'application/json': { schema: ProduitSchema } } },
    400: REPONSE_400,
    404: reponseErreur('Produit introuvable'),
  },
});


// ---- Commandes (protégées) ----


export const listerCommandesRoute = createRoute({
  method: 'get', path: '/commandes', tags: ['Commandes'],
  summary: 'Liste mes commandes', security: [{ Bearer: [] }],
  responses: {
    200: { description: 'Liste', content: { 'application/json': { schema: z.array(CommandeSchema) } } },
    401: reponseErreur('Token manquant ou invalide'),
  },
});


export const detailCommandeRoute = createRoute({
  method: 'get', path: '/commandes/{id}', tags: ['Commandes'],
  summary: "Détail d'une commande", security: [{ Bearer: [] }],
  request: { params: IdParamSchema },
  responses: {
    200: { description: 'Commande trouvée', content: { 'application/json': { schema: CommandeSchema } } },
    400: REPONSE_400,
    404: reponseErreur('Commande introuvable'),
  },
});


export const creerCommandeRoute = createRoute({
  method: 'post', path: '/commandes', tags: ['Commandes'],
  summary: 'Passer une commande', security: [{ Bearer: [] }],
  request: { body: { content: { 'application/json': { schema: CreerCommandeSchema } } } },
  responses: {
    201: { description: 'Commande créée', content: { 'application/json': { schema: CommandeSchema } } },
    400: REPONSE_400,
    404: reponseErreur('Produit introuvable'),
  },
});


export const supprimerCommandeRoute = createRoute({
  method: 'delete', path: '/commandes/{id}', tags: ['Commandes'],
  summary: 'Supprimer une commande', security: [{ Bearer: [] }],
  request: { params: IdParamSchema },
  responses: {
    200: { description: 'Commande supprimée', content: { 'application/json': { schema: MessageSchema } } },
    400: REPONSE_400,
    404: reponseErreur('Commande introuvable'),
  },
});
