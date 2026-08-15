import { pgTable, serial, text, integer, numeric, timestamp } from 'drizzle-orm/pg-core';


export const usersTable = pgTable('users', {
  id: serial('id').primaryKey(),
  email: text('email').notNull().unique(),
  motDePasse: text('mot_de_passe').notNull(),
  nom: text('nom').notNull(),
  // Utilisés par « mot de passe oublié » : un code temporaire et sa date limite.
  resetToken: text('reset_token'),
  resetTokenExpire: timestamp('reset_token_expire'),
});


export const categoriesTable = pgTable('categories', {
  id: serial('id').primaryKey(),
  libelle: text('libelle').notNull().unique(),
});


export const produitsTable = pgTable('produits', {
  id: serial('id').primaryKey(),
  libelle: text('libelle').notNull().unique(),
  prixUnitaire: numeric('prix_unitaire').notNull(),
  photoUrl: text('photo_url'),
  categorieId: integer('categorie_id')
    .references(() => categoriesTable.id)
    .notNull(),
});


export const commandesTable = pgTable('commandes', {
  id: serial('id').primaryKey(),
  userId: integer('user_id')
    .references(() => usersTable.id)
    .notNull(),
  total: numeric('total').notNull(),
  creeLe: timestamp('cree_le').notNull().defaultNow(),
});


export const lignesCommandeTable = pgTable('lignes_commande', {
  id: serial('id').primaryKey(),
  commandeId: integer('commande_id')
    .references(() => commandesTable.id, { onDelete: 'cascade' })
    .notNull(),
  produitId: integer('produit_id')
    .references(() => produitsTable.id)
    .notNull(),
  quantite: integer('quantite').notNull(),
  // On copie le prix du produit : si le produit change de prix plus tard,
  // la commande garde le prix payé le jour de l'achat.
  prixUnitaire: numeric('prix_unitaire').notNull(),
});
