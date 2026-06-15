import { Link } from 'react-router-dom';
import AssetImage from '../common/AssetImage.jsx';

export default function FeedPost({ post }) {
  return (
    <article className="smart-post">
      <header className="smart-post-header">
        <Link to={`/profile?user=${post.user?.id || post.userId || ''}`}><AssetImage name={post.user?.avatar || 'profile.png'} /></Link>
        <div><strong>{post.user?.name || post.authorName || 'Unknown user'}</strong><span>{post.createdAt || post.created_at || 'Just now'}</span></div>
      </header>
      <p>{post.body || post.content || post.text}</p>
      {post.image && <AssetImage name={post.image} className="smart-post-image" />}
      <footer className="smart-post-actions">
        <button type="button">Like {post.likesCount ? `(${post.likesCount})` : ''}</button>
        <button type="button">Comment {post.commentsCount ? `(${post.commentsCount})` : ''}</button>
        <button type="button">Share</button>
      </footer>
    </article>
  );
}
