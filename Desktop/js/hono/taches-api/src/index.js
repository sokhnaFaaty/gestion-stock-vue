import { serve } from '@hono/node-server';
import { OpenAPIHono } from '@hono/zod-openapi';
import { swaggerUI } from '@hono/swagger-ui';
 
import * as service from './taches.js';
import {
  listerRoute,
  creerRoute,
  terminerRoute,
  supprimerRoute,
  corbeilleRoute,
  restaurerRoute,
} from './routes.js';
 
const port = Number(process.env.PORT) || 3000;
const app = new OpenAPIHono();
 
app.get('/', (c) => {
  return c.json({
    message: 'API Gestion de tâches',
    documentation: '/ui',
  });
});
 
app.openapi(listerRoute, (c) => {
  return c.json(service.listerTaches(), 200);
});
 
app.openapi(creerRoute, (c) => {
  const { titre } = c.req.valid('json');
  const titreExisteDeja = service.titreExiste(titre);
  if(titreExisteDeja){
    return c.json(
      {erreur: `Une tache avec le titre "${titre}"existe deja`},
      409
    )
  }
  const nouvelleTache = service.creerTache(titre);
  return c.json(nouvelleTache, 201);
});
 
app.openapi(terminerRoute, (c) => {
  const { id } = c.req.valid('param');
  const tache = service.trouverTache(id);
 
  if (!tache) {
    return c.json(
    { erreur: `Aucune tâche active avec l'id ${id}.` },
    404
    );
  }
 
  return c.json(service.terminerTache(id), 200);
});
 
app.openapi(supprimerRoute, (c) => {
  const { id } = c.req.valid('param');
  const tache = service.trouverTache(id);
 
  if (!tache) {
    return c.json(
    { erreur: `Aucune tâche active avec l'id ${id}.` },
    404
    );
  }
 
  return c.json(service.supprimerTache(id), 200);
});
 
app.openapi(corbeilleRoute, (c) => {
  return c.json(service.listerCorbeille(), 200);
});
 
app.openapi(restaurerRoute, (c) => {
  const { id } = c.req.valid('param');
  const tache = service.trouverTache(id, {
    dansCorbeille: true,
  });
 
  if (!tache) {
    return c.json(
    { erreur: `Aucune tâche dans la corbeille avec l'id ${id}.` },
    404
    );
  }
 
  return c.json(service.restaurerTache(id), 200);
});
 
app.doc('/doc', {
  openapi: '3.0.0',
  info: {
    title: 'API Gestion de tâches',
    version: '1.0.0',
    description:
    'CRUD de tâches avec validation Zod et soft delete - E221',
  },
  servers: [
    {
    url: `http://localhost:${port}`,
    description: 'Serveur local',
    },
  ],
});
 
app.get('/ui', swaggerUI({ url: '/doc' }));
 
app.notFound((c) => {
  return c.json({ erreur: 'Route introuvable.' }, 404);
});
 
app.onError((erreur, c) => {
  console.error(erreur);
  return c.json(
    { erreur: 'Erreur interne du serveur.' },
    500
  );
});
 
console.log(`Serveur lancé sur http://localhost:${port}`);
console.log(`Swagger UI : http://localhost:${port}/ui`);
 
serve({
  fetch: app.fetch,
  port,
});
