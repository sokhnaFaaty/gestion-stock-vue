CREATE TABLE "categories" (
	"id" serial PRIMARY KEY NOT NULL,
	"libelle" text NOT NULL
);
--> statement-breakpoint
CREATE TABLE "produits" (
	"id" serial PRIMARY KEY NOT NULL,
	"libelle" text NOT NULL,
	"prix_unitaire" numeric NOT NULL,
	"photo_url" text,
	"categorie_id" integer NOT NULL
);
--> statement-breakpoint
CREATE TABLE "users" (
	"id" serial PRIMARY KEY NOT NULL,
	"email" text NOT NULL,
	"mot_de_passe" text NOT NULL,
	"nom" text NOT NULL,
	CONSTRAINT "users_email_unique" UNIQUE("email")
);
--> statement-breakpoint
ALTER TABLE "produits" ADD CONSTRAINT "produits_categorie_id_categories_id_fk" FOREIGN KEY ("categorie_id") REFERENCES "public"."categories"("id") ON DELETE no action ON UPDATE no action;