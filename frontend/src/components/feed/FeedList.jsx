import { EmptyState, ErrorState, LoadingState } from '../common/ApiState.jsx';
import FeedPost from './FeedPost.jsx';
import { useIntersectionLoadMore } from '../../hooks/useIntersectionLoadMore.js';

export default function FeedList({ infiniteState }) {
  const { items: posts, initialLoading, loading, error, hasMore, loadMore } = infiniteState;
  const loadMoreRef = useIntersectionLoadMore({
    enabled: hasMore && !loading && !initialLoading,
    onLoadMore: loadMore,
  });

  if (initialLoading) return <LoadingState label="Loading first feed page from API..." />;
  if (error && !posts.length) return <ErrorState error={error} />;
  if (!posts.length) return <EmptyState title="No feed posts found" subtitle="GET /user/feed will render paginated posts here when your backend is ready." />;

  return (
    <>
      {posts.map((post) => <FeedPost key={post.id} post={post} />)}

      {error && <ErrorState error={error} />}

      <div ref={loadMoreRef} className="feed-load-more-sentinel" aria-hidden="true" />

      {loading && <LoadingState label="Loading more posts..." />}
      {!hasMore && <div className="api-state text-center"><strong>You are all caught up</strong><span>No more posts right now.</span></div>}
    </>
  );
}
