import { OpenAPIHono } from '@hono/zod-openapi';
import { swaggerUI } from '@hono/swagger-ui';
import { serve } from '@hono/node-server';


import * as authService from './services/auth.js';
import * as categoriesService from './services/categories.js';
import * as produitsService from './services/produits.js';
import * as commandesService from './services/commandes.js';
import { authMiddleware } from './middlewares/auth.js';
import {
  inscrireRoute, connecterRoute, deconnecterRoute, changerMotDePasseRoute,
  motDePasseOublieRoute, reinitialiserMotDePasseRoute,
  listerCategoriesRoute, creerCategorieRoute, modifierCategorieRoute, supprimerCategorieRoute,
  listerProduitsRoute, detailProduitRoute, creerProduitRoute,
  modifierProduitRoute, remplacerPhotoRoute, supprimerProduitRoute,
  listerCommandesRoute, detailCommandeRoute, creerCommandeRoute, supprimerCommandeRoute
} from './routes.js';


// defaultHook : appelé automatiquement quand la validation Zod échoue.
// Sans lui, Hono renvoie une erreur brute illisible. Ici on renvoie un 400
// avec le détail champ par champ, ce que Swagger affiche tel quel.
const app = new OpenAPIHono({
  defaultHook: (result, c) => {
    if (!result.success) {
      return c.json(
        {
          erreur: 'Les données envoyées sont invalides.',
          details: result.error.issues.map((issue) => ({
            champ: issue.path.join('.') || '(corps de la requête)',
            message: issue.message,
          })),
        },
        400
      );
    }
  },
});


app.openAPIRegistry.registerComponent('securitySchemes', 'Bearer', {
  type: 'http', scheme: 'bearer', bearerFormat: 'JWT',
});


// ---- Auth (public) ----


app.openapi(inscrireRoute, async (c) => {
  const { nom, email, motDePasse } = c.req.valid('json');
  try {
    const user = await authService.inscrire(nom, email, motDePasse);
    return c.json(user, 201);
  } catch (e) {
    return c.json({ erreur: 'Cet email est déjà utilisé.' }, 409);
  }
});


app.openapi(connecterRoute, async (c) => {
  const { email, motDePasse } = c.req.valid('json');
  try {
    const resultat = await authService.connecter(email, motDePasse);
    return c.json(resultat, 200);
  } catch (e) {
    return c.json({ erreur: 'Email ou mot de passe incorrect.' }, 401);
  }
});


app.openapi(motDePasseOublieRoute, async (c) => {
  const { email } = c.req.valid('json');
  try {
    const token = await authService.demanderReinitialisation(email);
    return c.json({
      message: 'Code de réinitialisation généré (valable 15 minutes).',
      token,
    }, 200);
  } catch (e) {
    return c.json({ erreur: 'Aucun compte avec cet email.' }, 404);
  }
});


app.openapi(reinitialiserMotDePasseRoute, async (c) => {
  const { token, nouveauMotDePasse } = c.req.valid('json');
  try {
    await authService.reinitialiserMotDePasse(token, nouveauMotDePasse);
    return c.json({ message: 'Mot de passe réinitialisé, vous pouvez vous connecter.' }, 200);
  } catch (e) {
    return c.json({ erreur: 'Code invalide ou expiré.' }, 401);
  }
});


// ---- Protection : tout ce qui suit exige un Bearer token ----


app.use('/auth/logout', authMiddleware);
app.use('/auth/change-password', authMiddleware);
app.use('/categories/*', authMiddleware);
app.use('/produits/*', authMiddleware);
app.use('/commandes/*', authMiddleware);


// ---- Auth (protégé) ----


app.openapi(deconnecterRoute, async (c) => {
  // Un JWT ne se « supprime » pas côté serveur : il reste valide jusqu'à son
  // expiration (24 h). Le client doit donc effacer le token qu'il a stocké.
  return c.json({ message: 'Déconnexion réussie. Supprimez le token côté client.' }, 200);
});


app.openapi(changerMotDePasseRoute, async (c) => {
  const { ancienMotDePasse, nouveauMotDePasse } = c.req.valid('json');
  const userId = c.get('jwtPayload').sub;


  try {
    await authService.changerMotDePasse(userId, ancienMotDePasse, nouveauMotDePasse);
    return c.json({ message: 'Mot de passe modifié avec succès.' }, 200);
  } catch (e) {
    return c.json({ erreur: 'Ancien mot de passe incorrect.' }, 401);
  }
});


// ---- Catégories ----


app.openapi(listerCategoriesRoute, async (c) => {
  return c.json(await categoriesService.listerCategories());
});


app.openapi(creerCategorieRoute, async (c) => {
  const { libelle } = c.req.valid('json');


  const doublon = await categoriesService.trouverCategorieParLibelle(libelle);
  if (doublon) {
    return c.json({ erreur: `La catégorie « ${libelle} » existe déjà.` }, 409);
  }
  return c.json(await categoriesService.creerCategorie(libelle), 201);
});


app.openapi(modifierCategorieRoute, async (c) => {
  const { id } = c.req.valid('param');
  const { libelle } = c.req.valid('json');


  const existante = await categoriesService.trouverCategorie(id);
  if (!existante) {
    return c.json({ erreur: `Aucune catégorie avec l'id ${id}.` }, 404);
  }


  // Un doublon sur la catégorie qu'on modifie elle-même n'en est pas un.
  const doublon = await categoriesService.trouverCategorieParLibelle(libelle);
  if (doublon && doublon.id !== id) {
    return c.json({ erreur: `La catégorie « ${libelle} » existe déjà.` }, 409);
  }
  return c.json(await categoriesService.modifierCategorie(id, libelle));
});


app.openapi(supprimerCategorieRoute, async (c) => {
  const { id } = c.req.valid('param');


  const existante = await categoriesService.trouverCategorie(id);
  if (!existante) {
    return c.json({ erreur: `Aucune catégorie avec l'id ${id}.` }, 404);
  }
  return c.json(await categoriesService.supprimerCategorie(id));
});


// ---- Produits ----


app.openapi(listerProduitsRoute, async (c) => {
  return c.json(await produitsService.listerProduits());
});


app.openapi(detailProduitRoute, async (c) => {
  const { id } = c.req.valid('param');
  const produit = await produitsService.trouverProduit(id);
  if (!produit) {
    return c.json({ erreur: `Aucun produit avec l'id ${id}.` }, 404);
  }
  return c.json(produit);
});


app.openapi(creerProduitRoute, async (c) => {
  const { libelle, prixUnitaire, categorieId, photo } = c.req.valid('form');


  const doublon = await produitsService.trouverProduitParLibelle(libelle);
  if (doublon) {
    return c.json({ erreur: `Le produit « ${libelle} » existe déjà.` }, 409);
  }


  const produit = await produitsService.creerProduit({
    libelle,
    prixUnitaire,
    categorieId,
    fichierPhoto: photo ?? null,
  });


  return c.json(produit, 201);
});


app.openapi(modifierProduitRoute, async (c) => {
  const { id } = c.req.valid('param');
  const donnees = c.req.valid('json');


  const existant = await produitsService.trouverProduit(id);
  if (!existant) {
    return c.json({ erreur: `Aucun produit avec l'id ${id}.` }, 404);
  }


  if (donnees.libelle) {
    const doublon = await produitsService.trouverProduitParLibelle(donnees.libelle);
    if (doublon && doublon.id !== id) {
      return c.json({ erreur: `Le produit « ${donnees.libelle} » existe déjà.` }, 409);
    }
  }
  return c.json(await produitsService.modifierProduit(id, donnees));
});


app.openapi(remplacerPhotoRoute, async (c) => {
  const { id } = c.req.valid('param');
  const { photo } = c.req.valid('form');


  const existant = await produitsService.trouverProduit(id);
  if (!existant) {
    return c.json({ erreur: `Aucun produit avec l'id ${id}.` }, 404);
  }


  const produit = await produitsService.remplacerPhoto(id, photo);
  return c.json(produit);
});


app.openapi(supprimerProduitRoute, async (c) => {
  const { id } = c.req.valid('param');


  const existant = await produitsService.trouverProduit(id);
  if (!existant) {
    return c.json({ erreur: `Aucun produit avec l'id ${id}.` }, 404);
  }
  return c.json(await produitsService.supprimerProduit(id));
});


// ---- Commandes ----


app.openapi(listerCommandesRoute, async (c) => {
  const userId = c.get('jwtPayload').sub;
  return c.json(await commandesService.listerCommandes(userId));
});


app.openapi(detailCommandeRoute, async (c) => {
  const { id } = c.req.valid('param');
  const userId = c.get('jwtPayload').sub;


  const commande = await commandesService.trouverCommande(id);
  // On ne montre pas les commandes des autres utilisateurs.
  if (!commande || commande.userId !== userId) {
    return c.json({ erreur: `Aucune commande avec l'id ${id}.` }, 404);
  }
  return c.json(commande);
});


app.openapi(creerCommandeRoute, async (c) => {
  const { lignes } = c.req.valid('json');
  const userId = c.get('jwtPayload').sub;


  try {
    const commande = await commandesService.creerCommande(userId, lignes);
    return c.json(commande, 201);
  } catch (e) {
    const id = String(e.message).split(':')[1];
    return c.json({ erreur: `Aucun produit avec l'id ${id}.` }, 404);
  }
});


app.openapi(supprimerCommandeRoute, async (c) => {
  const { id } = c.req.valid('param');
  const userId = c.get('jwtPayload').sub;


  const commande = await commandesService.trouverCommande(id);
  if (!commande || commande.userId !== userId) {
    return c.json({ erreur: `Aucune commande avec l'id ${id}.` }, 404);
  }


  await commandesService.supprimerCommande(id);
  return c.json({ message: 'Commande supprimée.' }, 200);
});


app.doc('/doc', {
  openapi: '3.0.0',
  info: { title: 'API Gestion de stock', version: '1.0.0' },
});
app.get('/ui', swaggerUI({ url: '/doc' }));


serve({ fetch: app.fetch, port: 3000 });
console.log('Serveur sur http://localhost:3000/ui');
