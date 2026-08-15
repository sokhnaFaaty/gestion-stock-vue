import { createRoute, z } from '@hono/zod-openapi';
import {
  TacheSchema,
  CreerTacheSchema,
  IdParamSchema,
  ErreurSchema,
} from './schemas.js';
 
export const listerRoute = createRoute({
  method: 'get',
  path: '/taches',
  tags: ['Tâches'],
  summary: 'Lister les tâches actives',
  responses: {
    200: {
    description: 'Liste des tâches non supprimées',
    content: {
        'application/json': {
        schema: z.array(TacheSchema),
        },
    },
    },
  },
});
 
export const creerRoute = createRoute({
  method: 'post',
  path: '/taches',
  tags: ['Tâches'],
  summary: 'Créer une nouvelle tâche',
  request: {
    body: {
    content: {
        'application/json': {
        schema: CreerTacheSchema,
        },
    },
    },
  },
  responses: {
    201: {
    description: 'Tâche créée avec succès',
    content: {
        'application/json': {
        schema: TacheSchema,
        },
    },
    },
     409: {
    description: 'Une tache avec ce titre existe deja',
    content: {
        'application/json': {
        schema: ErreurSchema,
        },
    },
    },
  },
});
 
export const terminerRoute = createRoute({
  method: 'patch',
  path: '/taches/{id}/terminer',
  tags: ['Tâches'],
  summary: 'Marquer une tâche comme terminée',
  request: {
    params: IdParamSchema,
  },
  responses: {
    200: {
    description: 'Tâche mise à jour',
    content: {
        'application/json': {
        schema: TacheSchema,
        },
    },
    },
    404: {
    description: 'Tâche active introuvable',
    content: {
        'application/json': {
        schema: ErreurSchema,
        },
    },
    },
  },
});
 
export const supprimerRoute = createRoute({
  method: 'delete',
  path: '/taches/{id}',
  tags: ['Tâches'],
  summary: 'Supprimer une tâche de manière logique',
  request: {
    params: IdParamSchema,
  },
  responses: {
    200: {
    description: 'Tâche déplacée dans la corbeille',
    content: {
        'application/json': {
        schema: TacheSchema,
        },
    },
    },
    404: {
    description: 'Tâche active introuvable',
    content: {
        'application/json': {
        schema: ErreurSchema,
        },
    },
    },
  },
});
 
export const corbeilleRoute = createRoute({
  method: 'get',
  path: '/taches/corbeille',
  tags: ['Corbeille'],
  summary: 'Lister les tâches supprimées',
  responses: {
    200: {
    description: 'Liste des tâches présentes dans la corbeille',
    content: {
        'application/json': {
        schema: z.array(TacheSchema),
        },
    },
    },
  },
});
 
export const restaurerRoute = createRoute({
  method: 'post',
  path: '/taches/{id}/restaurer',
  tags: ['Corbeille'],
  summary: 'Restaurer une tâche depuis la corbeille',
  request: {
    params: IdParamSchema,
  },
  responses: {
    200: {
    description: 'Tâche restaurée avec succès',
    content: {
        'application/json': {
        schema: TacheSchema,
        },
    },
    },
    404: {
    description: 'Tâche introuvable dans la corbeille',
    content: {
        'application/json': {
        schema: ErreurSchema,
        },
    },
    },
  },
});
