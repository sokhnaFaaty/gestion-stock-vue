import { z } from '@hono/zod-openapi';
 
export const TacheSchema = z
  .object({
    id: z.number().int().positive().openapi({
    example: 1,
    description: 'Identifiant unique de la tâche',
    }),
    titre: z.string().min(1).max(120).openapi({
    example: 'Préparer le TD Hono',
    description: 'Titre lisible de la tâche',
    }),
    statut: z.enum(['active', 'terminee']).openapi({
    example: 'active',
    description: 'État d’avancement de la tâche',
    }),
    supprime: z.boolean().openapi({
    example: false,
    description: 'Indique si la tâche est dans la corbeille',
    }),
  })
  .openapi('Tache');
export const CreerTacheSchema = z
  .object({
    titre: z
    .string()
    .trim()
    .min(1, 'Le titre ne peut pas être vide.')
    .max(120, 'Le titre ne peut pas dépasser 120 caractères.')
    .openapi({
        example: 'Corriger les exercices async/await',
    }),
  })
  .openapi('CreerTache');
export const IdParamSchema = z.object({
  id: z.coerce.number().int().positive().openapi({
    param: {
    name: 'id',
    in: 'path',
    },
    example: 1,
    description: 'Identifiant numérique de la tâche',
  }),
});
export const ErreurSchema = z
  .object({
    erreur: z.string().openapi({
    example: "Aucune tâche active avec l'id 42.",
    }),
  })
  .openapi('Erreur');
