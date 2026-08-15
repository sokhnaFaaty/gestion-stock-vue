import bcrypt from 'bcryptjs';
import { randomUUID } from 'node:crypto';
import { sign } from 'hono/jwt';
import { db } from '../db/client.js';
import { usersTable } from '../db/schema.js';
import { eq } from 'drizzle-orm';
import 'dotenv/config';


const JWT_SECRET = process.env.JWT_SECRET;


export async function inscrire(nom, email, motDePasse) {
  const [existant] = await db
    .select()
    .from(usersTable)
    .where(eq(usersTable.email, email));


  if (existant) {
    throw new Error('EMAIL_DEJA_UTILISE');
  }


  const hash = await bcrypt.hash(motDePasse, 10);


  const [user] = await db
    .insert(usersTable)
    .values({ nom, email, motDePasse: hash })
    .returning({
      id: usersTable.id,
      nom: usersTable.nom,
      email: usersTable.email,
    });


  return user;
}


export async function connecter(email, motDePasse) {
  const [user] = await db
    .select()
    .from(usersTable)
    .where(eq(usersTable.email, email));


  if (!user) {
    throw new Error('IDENTIFIANTS_INVALIDES');
  }


  const motDePasseValide = await bcrypt.compare(motDePasse, user.motDePasse);
  if (!motDePasseValide) {
    throw new Error('IDENTIFIANTS_INVALIDES');
  }


  const payload = {
    sub: user.id,
    email: user.email,
    exp: Math.floor(Date.now() / 1000) + 60 * 60 * 24,
  };
  const token = await sign(payload, JWT_SECRET);


  return {
    token,
    user: { id: user.id, nom: user.nom, email: user.email },
  };
}


export async function changerMotDePasse(userId, ancienMotDePasse, nouveauMotDePasse) {
  const [user] = await db
    .select()
    .from(usersTable)
    .where(eq(usersTable.id, userId));


  if (!user) {
    throw new Error('IDENTIFIANTS_INVALIDES');
  }


  const ancienValide = await bcrypt.compare(ancienMotDePasse, user.motDePasse);
  if (!ancienValide) {
    throw new Error('ANCIEN_MOT_DE_PASSE_INCORRECT');
  }


  const hash = await bcrypt.hash(nouveauMotDePasse, 10);
  await db
    .update(usersTable)
    .set({ motDePasse: hash })
    .where(eq(usersTable.id, userId));
}


// Génère un code valable 15 minutes et l'enregistre sur l'utilisateur.
export async function demanderReinitialisation(email) {
  const [user] = await db
    .select()
    .from(usersTable)
    .where(eq(usersTable.email, email));


  if (!user) {
    throw new Error('UTILISATEUR_INTROUVABLE');
  }


  const token = randomUUID();
  const expire = new Date(Date.now() + 15 * 60 * 1000);


  await db
    .update(usersTable)
    .set({ resetToken: token, resetTokenExpire: expire })
    .where(eq(usersTable.id, user.id));


  return token;
}


export async function reinitialiserMotDePasse(token, nouveauMotDePasse) {
  const [user] = await db
    .select()
    .from(usersTable)
    .where(eq(usersTable.resetToken, token));


  if (!user || !user.resetTokenExpire || user.resetTokenExpire < new Date()) {
    throw new Error('CODE_INVALIDE');
  }


  const hash = await bcrypt.hash(nouveauMotDePasse, 10);


  // On efface le code : il ne doit servir qu'une seule fois.
  await db
    .update(usersTable)
    .set({ motDePasse: hash, resetToken: null, resetTokenExpire: null })
    .where(eq(usersTable.id, user.id));
}
