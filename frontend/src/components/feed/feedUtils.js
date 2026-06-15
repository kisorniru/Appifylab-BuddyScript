import { fallbackPost } from '../../data/feedStaticData.js';

export function formatFeedTime(value) {
  if (!value) return '5 minute ago';

  const timestamp = new Date(value).getTime();
  if (Number.isNaN(timestamp)) return '5 minute ago';

  const diffInSeconds = Math.max(1, Math.floor((Date.now() - timestamp) / 1000));
  if (diffInSeconds < 60) return 'Just now';

  const diffInMinutes = Math.floor(diffInSeconds / 60);
  if (diffInMinutes < 60) return `${diffInMinutes} minute${diffInMinutes === 1 ? '' : 's'} ago`;

  const diffInHours = Math.floor(diffInMinutes / 60);
  if (diffInHours < 24) return `${diffInHours} hour${diffInHours === 1 ? '' : 's'} ago`;

  const diffInDays = Math.floor(diffInHours / 24);
  return `${diffInDays} day${diffInDays === 1 ? '' : 's'} ago`;
}

export function normalizeTimelinePost(post) {
  const source = post || fallbackPost;

  return {
    id: source.id,
    postId: source.postId || source.post_id || source.id,
    authorName: source.author?.name || source.authorName || 'Unknown user',
    authorAvatar: source.author?.avatar || source.authorAvatar || 'profile.png',
    text: source.text || source.body || '-Healthy Tracking App',
    image: source.image || source.mediaPreviewUrl || null,
    mediaType: source.mediaType ?? source.media_type ?? null,
    reactionCount: source.reactionCount ?? source.likesCount ?? 0,
    reactors: source.reactors || [],
    commentCount: source.commentCount ?? source.commentsCount ?? 0,
    shareCount: source.shareCount ?? 0,
    viewerReaction: source.viewerReaction ?? null,
    createdAt: source.createdAt || source.created_at || null,
  };
}

export function normalizeComment(comment) {
  const source = comment || {};

  return {
    id: source.id,
    postId: source.postId || source.post_id,
    parentId: source.parentId || source.parent_id || null,
    authorName: source.author?.name || source.authorName || 'Unknown user',
    authorAvatar: source.author?.avatar || source.authorAvatar || 'profile.png',
    body: source.body || source.text || '',
    media: source.media || [],
    reactionCount: source.reactionCount ?? source.reactions_count ?? 0,
    reactors: source.reactors || [],
    replyCount: source.replyCount ?? source.replies_count ?? 0,
    viewerReaction: source.viewerReaction ?? source.viewer_reaction ?? null,
    createdAt: source.createdAt || source.created_at || null,
  };
}

export function formatCompactFeedTime(value) {
  if (!value) return '21m';

  const timestamp = new Date(value).getTime();
  if (Number.isNaN(timestamp)) return '21m';

  const diffInSeconds = Math.max(1, Math.floor((Date.now() - timestamp) / 1000));
  if (diffInSeconds < 60) return 'now';

  const diffInMinutes = Math.floor(diffInSeconds / 60);
  if (diffInMinutes < 60) return `${diffInMinutes}m`;

  const diffInHours = Math.floor(diffInMinutes / 60);
  if (diffInHours < 24) return `${diffInHours}h`;

  return `${Math.floor(diffInHours / 24)}d`;
}

export function isVideoMedia(mediaUrl = '', mediaType = null) {
  return Number(mediaType) === 2 || /\.(webm|mp4|mov|m4v|avi)(\?.*)?$/i.test(String(mediaUrl));
}

export function getVideoMimeType(mediaUrl = '') {
  const url = String(mediaUrl).toLowerCase();

  if (url.includes('.webm')) return 'video/webm';
  if (url.includes('.mov')) return 'video/quicktime';
  if (url.includes('.m4v')) return 'video/x-m4v';

  return 'video/mp4';
}

export function getCommentMediaUrl(media = {}) {
  return media.fileUrl || media.file_url || media.url || '';
}

export function getCommentMediaThumb(media = {}) {
  return media.thumbnailUrl || media.thumbnail_url || getCommentMediaUrl(media);
}

export function isAudioCommentMedia(media = {}) {
  return Number(media.mediaType ?? media.media_type) === 3 || String(media.mimeType || media.mime_type || '').startsWith('audio/');
}

export function normalizeNotification(notification) {
  const source = notification || {};
  const sender = source.sender || {};
  const data = source.data || {};

  return {
    id: source.id,
    type: source.type || '',
    senderName: sender.name || '',
    senderAvatar: sender.avatar || 'profile.png',
    message: source.message || data.message || '',
    isRead: Boolean(source.isRead ?? source.is_read),
    createdAt: source.createdAt || source.created_at || null,
  };
}

export function normalizeReactionUser(reaction) {
  const source = reaction || {};
  const user = source.user || {};

  return {
    id: source.id,
    userName: user.name || 'Unknown user',
    userAvatar: user.avatar || 'profile.png',
    reactionType: source.reactionType ?? source.reaction_type ?? 1,
    reactedAt: source.reactedAt || source.reacted_at || null,
  };
}
