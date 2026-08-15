CREATE TABLE "commandes" (
	"id" serial PRIMARY KEY NOT NULL,
	"user_id" integer NOT NULL,
	"total" numeric NOT NULL,
	"cree_le" timestamp DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "lignes_commande" (
	"id" serial PRIMARY KEY NOT NULL,
	"commande_id" integer NOT NULL,
	"produit_id" integer NOT NULL,
	"quantite" integer NOT NULL,
	"prix_unitaire" numeric NOT NULL
);
--> statement-breakpoint
ALTER TABLE "users" ADD COLUMN "reset_token" text;--> statement-breakpoint
ALTER TABLE "users" ADD COLUMN "reset_token_expire" timestamp;--> statement-breakpoint
ALTER TABLE "commandes" ADD CONSTRAINT "commandes_user_id_users_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "lignes_commande" ADD CONSTRAINT "lignes_commande_commande_id_commandes_id_fk" FOREIGN KEY ("commande_id") REFERENCES "public"."commandes"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "lignes_commande" ADD CONSTRAINT "lignes_commande_produit_id_produits_id_fk" FOREIGN KEY ("produit_id") REFERENCES "public"."produits"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "categories" ADD CONSTRAINT "categories_libelle_unique" UNIQUE("libelle");--> statement-breakpoint
ALTER TABLE "produits" ADD CONSTRAINT "produits_libelle_unique" UNIQUE("libelle");