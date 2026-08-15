import { db } from '../db/client.js';
import { commandesTable, lignesCommandeTable, produitsTable } from '../db/schema.js';
import { eq, inArray } from 'drizzle-orm';


// Récupère une commande avec ses lignes.
export async function trouverCommande(id) {
  const [commande] = await db
    .select()
    .from(commandesTable)
    .where(eq(commandesTable.id, id));


  if (!commande) return undefined;


  const lignes = await db
    .select()
    .from(lignesCommandeTable)
    .where(eq(lignesCommandeTable.commandeId, id));


  return { ...commande, lignes };
}


export async function listerCommandes(userId) {
  const commandes = await db
    .select()
    .from(commandesTable)
    .where(eq(commandesTable.userId, userId));


  // Pour chaque commande on va chercher ses lignes.
  const resultat = [];
  for (const commande of commandes) {
    const lignes = await db
      .select()
      .from(lignesCommandeTable)
      .where(eq(lignesCommandeTable.commandeId, commande.id));
    resultat.push({ ...commande, lignes });
  }
  return resultat;
}


export async function creerCommande(userId, lignesDemandees) {
  // 1. On récupère les produits demandés pour connaître leur vrai prix.
  const ids = lignesDemandees.map((l) => l.produitId);
  const produits = await db
    .select()
    .from(produitsTable)
    .where(inArray(produitsTable.id, ids));


  // 2. Si un produit n'existe pas, on arrête tout de suite.
  for (const ligne of lignesDemandees) {
    const produit = produits.find((p) => p.id === ligne.produitId);
    if (!produit) {
      throw new Error(`PRODUIT_INTROUVABLE:${ligne.produitId}`);
    }
  }


  // 3. On calcule le total côté serveur (jamais depuis le client).
  let total = 0;
  for (const ligne of lignesDemandees) {
    const produit = produits.find((p) => p.id === ligne.produitId);
    total += Number(produit.prixUnitaire) * ligne.quantite;
  }


  const [commande] = await db
    .insert(commandesTable)
    .values({ userId, total: String(total) })
    .returning();


  const lignes = await db
    .insert(lignesCommandeTable)
    .values(
      lignesDemandees.map((ligne) => ({
        commandeId: commande.id,
        produitId: ligne.produitId,
        quantite: ligne.quantite,
        prixUnitaire: produits.find((p) => p.id === ligne.produitId).prixUnitaire,
      }))
    )
    .returning();


  return { ...commande, lignes };
}


export async function supprimerCommande(id) {
  // Les lignes partent automatiquement grâce au « onDelete: cascade ».
  await db.delete(commandesTable).where(eq(commandesTable.id, id));
}
