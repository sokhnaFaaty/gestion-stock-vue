const BASE_URL = import.meta.env.VITE_API_URL;
const TOKEN_KEY = 'token';
const USER_KEY = 'user';
 
function getToken() {
  return localStorage.getItem(TOKEN_KEY);
}
 
function viderSessionLocale() {
  localStorage.removeItem(TOKEN_KEY);
  localStorage.removeItem(USER_KEY);
}
 
function preparerBody(body) {
  if (body == null) {
    return undefined;
  }
 
  return JSON.stringify(body);
}
 
async function lireReponse(reponse) {
  if (reponse.status === 204) {
    return null;
  }
 
  const contentType = reponse.headers.get('content-type') || '';
 
  if (contentType.includes('application/json')) {
    return reponse.json().catch(() => null);
  }
 
  return reponse.text().catch(() => null);
}
 
async function requete(chemin, options = {}) {
  const token = getToken();
  const headers = { ...options.headers };
 
  if (options.body != null) {
    headers['Content-Type'] = 'application/json';
  }
 
  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }
 
  let reponse;
 
  try {
    reponse = await fetch(`${BASE_URL}${chemin}`, {
      ...options,
      headers,
      body: preparerBody(options.body),
    });
  } catch {
    throw new Error('Impossible de joindre le serveur.');
  }
 
  const data = await lireReponse(reponse);
 
  if (reponse.status === 401 && token) {
    viderSessionLocale();
 
    if (window.location.pathname !== '/login') {
      window.location.replace('/login');
    }
 
    throw new Error('Session expirée, reconnectez-vous.');
  }
 
  if (!reponse.ok) {
    const message =
      typeof data === 'object' && data !== null
        ? data.erreur || data.message
        : data;
 
    throw new Error(message || 'Une erreur est survenue.');
  }
 
  return data;
}
 
export const apiClient = {
  get: (chemin) => requete(chemin, { method: 'GET' }),
  post: (chemin, body) => requete(chemin, { method: 'POST', body }),
  put: (chemin, body) => requete(chemin, { method: 'PUT', body }),
  delete: (chemin) => requete(chemin, { method: 'DELETE' }),
};
