import { z } from '@hono/zod-openapi';
// ---- Auth ----


export const InscriptionSchema = z.object({
  nom: z.string().min(1),
  email: z.string().email(),
  motDePasse: z.string().min(6, 'Minimum 6 caractères'),
}).openapi('Inscription');


export const ConnexionSchema = z.object({
  email: z.string().email(),
  motDePasse: z.string(),
}).openapi('Connexion');


export const UtilisateurSchema = z.object({
  id: z.number(),
  nom: z.string(),
  email: z.string(),
}).openapi('Utilisateur');


export const TokenSchema = z.object({
  token: z.string(),
  user: UtilisateurSchema,
}).openapi('Token');


export const ChangerMotDePasseSchema = z.object({
  ancienMotDePasse: z.string(),
  nouveauMotDePasse: z.string().min(6, 'Minimum 6 caractères'),
}).openapi('ChangerMotDePasse');


export const MotDePasseOublieSchema = z.object({
  email: z.string().email(),
}).openapi('MotDePasseOublie');


// En vrai on enverrait le code par email. Ici on le renvoie dans la réponse
// pour pouvoir tester directement depuis Swagger.
export const CodeResetSchema = z.object({
  message: z.string(),
  token: z.string(),
}).openapi('CodeReset');


export const ReinitialiserSchema = z.object({
  token: z.string(),
  nouveauMotDePasse: z.string().min(6, 'Minimum 6 caractères'),
}).openapi('Reinitialiser');


export const MessageSchema = z.object({
  message: z.string(),
}).openapi('Message');


// ---- Catégorie ----


export const CategorieSchema = z.object({
  id: z.number(),
  libelle: z.string(),
}).openapi('Categorie');


export const CreerCategorieSchema = z.object({
  libelle: z.string().min(1),
}).openapi('CreerCategorie');


// ---- Produit ----


export const ProduitSchema = z.object({
  id: z.number(),
  libelle: z.string(),
  prixUnitaire: z.string(),
  photoUrl: z.string().nullable(),
  categorieId: z.number(),
}).openapi('Produit');


export const ModifierProduitSchema = z.object({
  libelle: z.string().min(1).optional(),
  prixUnitaire: z.string().optional(),
  categorieId: z.number().optional(),
}).openapi('ModifierProduit');


// Schéma réutilisable pour un champ fichier dans un formulaire multipart
export const FileSchema = z
  .custom((val) => val instanceof File, 'Un fichier est requis')
  .openapi({ type: 'string', format: 'binary' });


export const CreerProduitFormSchema = z.object({
  libelle: z.string().min(1),
  prixUnitaire: z.string(),
  categorieId: z.coerce.number(),
  photo: FileSchema.optional(),
}).openapi('CreerProduitForm');


export const RemplacerPhotoFormSchema = z.object({
  photo: FileSchema,
}).openapi('RemplacerPhotoForm');


// ---- Commande ----


export const CreerCommandeSchema = z.object({
  lignes: z.array(
    z.object({
      produitId: z.number(),
      quantite: z.number().int().min(1, 'La quantité doit être au moins 1'),
    })
  ).min(1, 'Il faut au moins un produit'),
}).openapi('CreerCommande');


export const LigneCommandeSchema = z.object({
  id: z.number(),
  produitId: z.number(),
  quantite: z.number(),
  prixUnitaire: z.string(),
}).openapi('LigneCommande');


export const CommandeSchema = z.object({
  id: z.number(),
  userId: z.number(),
  total: z.string(),
  creeLe: z.string(),
  lignes: z.array(LigneCommandeSchema),
}).openapi('Commande');


// ---- Commun ----


export const IdParamSchema = z.object({
  id: z.coerce.number().openapi({ param: { name: 'id', in: 'path' } }),
});


export const ErreurSchema = z.object({
  erreur: z.string(),
}).openapi('Erreur');


// Réponse renvoyée quand la validation échoue : le message global + le détail
// champ par champ, pour que Swagger montre exactement ce qui ne va pas.
export const ErreurValidationSchema = z.object({
  erreur: z.string(),
  details: z.array(
    z.object({
      champ: z.string(),
      message: z.string(),
    })
  ),
}).openapi('ErreurValidation');
