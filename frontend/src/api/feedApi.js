import { request, unwrapData } from './apiClient';

function toPostFormData(payload = {}) {
  if (payload instanceof FormData) return payload;

  const formData = new FormData();
  if (payload.title) formData.append('title', payload.title);
  if (payload.body) formData.append('body', payload.body);
  if (payload.visibility) formData.append('visibility', payload.visibility);
  if (payload.media) formData.append('media', payload.media);
  if (payload.thumbnail) formData.append('thumbnail', payload.thumbnail);
  if (payload.mediaUrl) formData.append('mediaUrl', payload.mediaUrl);
  if (payload.thumbnailUrl) formData.append('thumbnailUrl', payload.thumbnailUrl);
  if (payload.mediaType) formData.append('mediaType', payload.mediaType);

  return formData;
}

function toCommentFormData(payload = {}) {
  if (payload instanceof FormData) return payload;

  const formData = new FormData();
  if (payload.postId) formData.append('postId', payload.postId);
  if (payload.parentId) formData.append('parentId', payload.parentId);
  if (payload.body) formData.append('body', payload.body);
  if (payload.media) formData.append('media', payload.media);
  if (payload.mediaType) formData.append('mediaType', payload.mediaType);

  return formData;
}

export const feedApi = {
  getUserFeed: ({ cursor = null, limit = 10 } = {}) =>
    request('/user/feed', { params: { cursor, limit } }).then((payload) => ({
      items: unwrapData(payload)?.items || unwrapData(payload) || [],
      meta: payload?.meta || unwrapData(payload)?.meta || {},
    })),

  getNotifications: ({ cursor = null, limit = 10, unreadOnly = false } = {}) =>
    request('/user/notification', { params: { cursor, limit, unreadOnly: unreadOnly ? 1 : null } }).then((payload) => ({
      items: unwrapData(payload)?.items || unwrapData(payload) || [],
      meta: payload?.meta || unwrapData(payload)?.meta || {},
    })),

  markNotificationAsRead: (notificationId) =>
    request('/user/notification/read', {
      method: 'POST',
      body: JSON.stringify({ notificationId }),
    }).then(unwrapData),

  getStories: ({ cursor = null, limit = 12 } = {}) =>
    request('/stories', { params: { cursor, limit } }).then((payload) => ({
      items: unwrapData(payload)?.items || unwrapData(payload) || [],
      meta: payload?.meta || unwrapData(payload)?.meta || {},
    })),

  getFriendSuggestions: ({ cursor = null, limit = 8 } = {}) =>
    request('/friends/suggestions', { params: { cursor, limit } }).then((payload) => ({
      items: unwrapData(payload)?.items || unwrapData(payload) || [],
      meta: payload?.meta || unwrapData(payload)?.meta || {},
    })),

  getEvents: ({ cursor = null, limit = 5 } = {}) =>
    request('/events', { params: { cursor, limit } }).then((payload) => ({
      items: unwrapData(payload)?.items || unwrapData(payload) || [],
      meta: payload?.meta || unwrapData(payload)?.meta || {},
    })),

  createPost: (payload) =>
    request('/user/post', {
      method: 'POST',
      body: toPostFormData(payload),
    }).then(unwrapData),

  togglePostReaction: ({ postId, reactionType = 1 }) =>
    request('/user/post/reaction', {
      method: 'POST',
      body: JSON.stringify({ postId, reactionType }),
    }).then(unwrapData),

  getPostReactionUsers: ({ postId, cursor = null, limit = 10 } = {}) =>
    request('/user/post/reaction/user', { params: { postId, cursor, limit } }).then((payload) => ({
      items: unwrapData(payload)?.items || unwrapData(payload) || [],
      meta: payload?.meta || unwrapData(payload)?.meta || {},
    })),

  getCommentReactionUsers: ({ commentId, cursor = null, limit = 10 } = {}) =>
    request('/user/post/comment/reaction/user', { params: { commentId, cursor, limit } }).then((payload) => ({
      items: unwrapData(payload)?.items || unwrapData(payload) || [],
      meta: payload?.meta || unwrapData(payload)?.meta || {},
    })),

  getPostComments: ({ postId, parentId = null, cursor = null, limit = 3 } = {}) =>
    request('/user/post/comment', { params: { postId, parentId, cursor, limit } }).then((payload) => ({
      items: unwrapData(payload)?.items || unwrapData(payload) || [],
      meta: payload?.meta || unwrapData(payload)?.meta || {},
    })),

  createPostComment: ({ postId, parentId = null, body = '', media = null, mediaType = null }) =>
    request('/user/post/comment', {
      method: 'POST',
      body: media
        ? toCommentFormData({ postId, parentId, body, media, mediaType })
        : JSON.stringify({ postId, parentId, body }),
    }).then(unwrapData),

  toggleCommentReaction: ({ commentId, reactionType = 1 }) =>
    request('/user/post/comment/reaction', {
      method: 'POST',
      body: JSON.stringify({ commentId, reactionType }),
    }).then(unwrapData),
};
