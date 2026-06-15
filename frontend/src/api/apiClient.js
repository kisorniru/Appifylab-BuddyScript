const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080/api/v1';
const CLIENT_SECRET_KEY = import.meta.env.VITE_CLIENT_SECRET_KEY || 'base64:S0FZaUFwaURPTC1raXNvcm5pcnVAZ21haWw=';

const TOKEN_KEY = 'buddyscript_api_token';
const USER_KEY = 'buddyscript_auth_user';

export class ApiError extends Error {
  constructor(message, status, payload) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.payload = payload;
    this.errors = payload?.errors || null;
  }
}

export const authStorage = {
  getToken: () => localStorage.getItem(TOKEN_KEY),
  setToken: (token) => token && localStorage.setItem(TOKEN_KEY, token),
  clearToken: () => localStorage.removeItem(TOKEN_KEY),
  getUser: () => {
    try {
      return JSON.parse(localStorage.getItem(USER_KEY) || 'null');
    } catch {
      return null;
    }
  },
  setUser: (user) => user && localStorage.setItem(USER_KEY, JSON.stringify(user)),
  clearUser: () => localStorage.removeItem(USER_KEY),
  clear: () => {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
    localStorage.removeItem('access_token');
  },
};

function buildUrl(path, params = {}) {
  const normalizedPath = path.startsWith('/') ? path : `/${path}`;
  const url = new URL(`${API_BASE_URL}${normalizedPath}`);
  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== '') url.searchParams.set(key, value);
  });
  return url.toString();
}

function getApiMessage(payload, fallback) {
  if (!payload) return fallback;
  if (payload.message) return payload.message;
  if (payload.errors) {
    const firstError = Object.values(payload.errors).flat()[0];
    if (firstError) return firstError;
  }
  return fallback;
}

export async function request(path, options = {}) {
  const { params, clientAuth = false, auth = true, ...fetchOptions } = options;
  const token = authStorage.getToken() || localStorage.getItem('access_token');
  const isFormData = fetchOptions.body instanceof FormData;
  const headers = {
    Accept: 'application/json',
    ...(fetchOptions.body && !isFormData ? { 'Content-Type': 'application/json' } : {}),
    ...(clientAuth ? { 'X-Client-Key': CLIENT_SECRET_KEY } : {}),
    ...(auth && token ? { Authorization: `Bearer ${token}` } : {}),
    ...fetchOptions.headers,
  };

  const response = await fetch(buildUrl(path, params), {
    ...fetchOptions,
    headers,
  });

  const contentType = response.headers.get('content-type') || '';
  const payload = contentType.includes('application/json') ? await response.json() : null;

  if (!response.ok) {
    if (response.status === 401) authStorage.clear();
    throw new ApiError(getApiMessage(payload, 'API request failed'), response.status, payload);
  }

  return payload;
}

export function unwrapData(payload) {
  return payload?.responseBody ?? payload?.data ?? payload;
}
