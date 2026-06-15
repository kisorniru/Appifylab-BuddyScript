import { authStorage, request, unwrapData } from './apiClient';

function normalizeAuthenticatedUser(payload) {
  const body = unwrapData(payload);
  if (!body) return null;

  return {
    id: body.id,
    name: body.name,
    email: body.email,
    firstTimeLogin: Boolean(body.firstTimeLogin),
    apiToken: body.apiToken,
    avatar: body.avatar || 'profile.png',
  };
}

function persistAuth(payload) {
  const user = normalizeAuthenticatedUser(payload);
  if (user?.apiToken) authStorage.setToken(user.apiToken);
  if (user) authStorage.setUser(user);
  return user;
}

function toRegistrationPayload(form) {
  const [firstName, ...rest] = String(form.name || '').trim().split(/\s+/).filter(Boolean);
  return {
    firstName: form.firstName || firstName || '',
    lastName: form.lastName || rest.join(' ') || '',
    email: form.email,
  };
}

function pemToArrayBuffer(pem) {
  const base64 = pem
    .replace(/-----BEGIN PUBLIC KEY-----/g, '')
    .replace(/-----END PUBLIC KEY-----/g, '')
    .replace(/\s/g, '');
  const binary = atob(base64);
  const bytes = new Uint8Array(binary.length);

  for (let index = 0; index < binary.length; index += 1) {
    bytes[index] = binary.charCodeAt(index);
  }

  return bytes.buffer;
}

function arrayBufferToBase64(buffer) {
  const bytes = new Uint8Array(buffer);
  let binary = '';

  bytes.forEach((byte) => {
    binary += String.fromCharCode(byte);
  });

  return btoa(binary);
}

async function fetchCredentialKey() {
  const payload = await request('/user/auth/credential-key', {
    method: 'GET',
    clientAuth: true,
    auth: false,
  });

  return unwrapData(payload);
}

async function importCredentialPublicKey(publicKey) {
  return window.crypto.subtle.importKey(
    'spki',
    pemToArrayBuffer(publicKey),
    { name: 'RSA-OAEP', hash: 'SHA-1' },
    false,
    ['encrypt'],
  );
}

async function encryptCredential(publicKey, value) {
  const encodedValue = new TextEncoder().encode(String(value));
  const encryptedValue = await window.crypto.subtle.encrypt({ name: 'RSA-OAEP' }, publicKey, encodedValue);

  return arrayBufferToBase64(encryptedValue);
}

async function buildEncryptedCredentials(values) {
  if (!window.crypto?.subtle) {
    throw new Error('Secure credential encryption is not available in this browser.');
  }

  const credentialKey = await fetchCredentialKey();
  const publicKey = await importCredentialPublicKey(credentialKey.publicKey);
  const encryptedValues = {};

  for (const [key, value] of Object.entries(values)) {
    encryptedValues[key] = await encryptCredential(publicKey, value);
  }

  return {
    credentialKeyId: credentialKey.credentialKeyId,
    ...encryptedValues,
  };
}

export const authApi = {
  login: async (credentials) => {
    const encryptedCredentials = await buildEncryptedCredentials({
      password: credentials.password,
    });

    return request('/user/login', {
      method: 'POST',
      clientAuth: true,
      auth: false,
      body: JSON.stringify({ email: credentials.email, ...encryptedCredentials }),
    }).then((payload) => persistAuth(payload));
  },

  register: async (form) => {
    const encryptedCredentials = await buildEncryptedCredentials({
      password: form.password,
      passwordConfirm: form.passwordConfirm || form.password_confirmation,
    });

    return request('/user/registration', {
      method: 'POST',
      clientAuth: true,
      auth: false,
      body: JSON.stringify({ ...toRegistrationPayload(form), ...encryptedCredentials }),
    }).then((payload) => persistAuth(payload));
  },

  socialLogin: (payload) =>
    request('/user/social-login', {
      method: 'POST',
      clientAuth: true,
      auth: false,
      body: JSON.stringify(payload),
    }).then((response) => persistAuth(response)),

  me: async () => authStorage.getUser(),

  logout: async () => {
    try {
      await request('/user/logout', { method: 'POST' });
    } finally {
      authStorage.clear();
    }
  },

  logoutAll: async () => {
    try {
      await request('/user/logout-all', { method: 'POST' });
    } finally {
      authStorage.clear();
    }
  },

  getStoredUser: () => authStorage.getUser(),
  getToken: () => authStorage.getToken(),
  isAuthenticated: () => Boolean(authStorage.getToken() || localStorage.getItem('access_token')),
  clear: () => authStorage.clear(),
};
