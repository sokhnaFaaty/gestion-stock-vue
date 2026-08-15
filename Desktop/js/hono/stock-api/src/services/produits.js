import { db } from '../db/client.js';
import { produitsTable } from '../db/schema.js';
import { eq } from 'drizzle-orm';
import { uploadImage } from '../config/cloudinary.js';


export async function listerProduits() {
  return db.select().from(produitsTable);
}


export async function trouverProduit(id) {
  const [produit] = await db
    .select()
    .from(produitsTable)
    .where(eq(produitsTable.id, id));
  return produit;
}


// Sert à vérifier qu'un libellé n'est pas déjà pris avant d'insérer/modifier.
export async function trouverProduitParLibelle(libelle) {
  const [produit] = await db
    .select()
    .from(produitsTable)
    .where(eq(produitsTable.libelle, libelle));
  return produit;
}


export async function creerProduit({ libelle, prixUnitaire, categorieId, fichierPhoto }) {
  let photoUrl = null;


  if (fichierPhoto) {
    const buffer = Buffer.from(await fichierPhoto.arrayBuffer());
    const resultat = await uploadImage(buffer);
    photoUrl = resultat.secure_url;
  }


  const [produit] = await db
    .insert(produitsTable)
    .values({ libelle, prixUnitaire, categorieId, photoUrl })
    .returning();


  return produit;
}


export async function modifierProduit(id, donnees) {
  const [produit] = await db
    .update(produitsTable)
    .set(donnees)
    .where(eq(produitsTable.id, id))
    .returning();
  return produit;
}


export async function remplacerPhoto(id, fichierPhoto) {
  const buffer = Buffer.from(await fichierPhoto.arrayBuffer());
  const resultat = await uploadImage(buffer);


  const [produit] = await db
    .update(produitsTable)
    .set({ photoUrl: resultat.secure_url })
    .where(eq(produitsTable.id, id))
    .returning();


  return produit;
}


export async function supprimerProduit(id) {
  const [produit] = await db
    .delete(produitsTable)
    .where(eq(produitsTable.id, id))
    .returning();
  return produit;
}
