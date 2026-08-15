import { db } from '../db/client.js';
import { categoriesTable } from '../db/schema.js';
import { eq } from 'drizzle-orm';


export async function listerCategories() {
  return db.select().from(categoriesTable);
}


// Sert à vérifier qu'un libellé n'est pas déjà pris avant d'insérer/modifier.
export async function trouverCategorieParLibelle(libelle) {
  const [categorie] = await db
    .select()
    .from(categoriesTable)
    .where(eq(categoriesTable.libelle, libelle));
  return categorie;
}


export async function creerCategorie(libelle) {
  const [categorie] = await db
    .insert(categoriesTable)
    .values({ libelle })
    .returning();
  return categorie;
}


export async function trouverCategorie(id) {
  const [categorie] = await db
    .select()
    .from(categoriesTable)
    .where(eq(categoriesTable.id, id));
  return categorie;
}


export async function modifierCategorie(id, libelle) {
  const [categorie] = await db
    .update(categoriesTable)
    .set({ libelle })
    .where(eq(categoriesTable.id, id))
    .returning();
  return categorie;
}


export async function supprimerCategorie(id) {
  const [categorie] = await db
    .delete(categoriesTable)
    .where(eq(categoriesTable.id, id))
    .returning();
  return categorie;
}
