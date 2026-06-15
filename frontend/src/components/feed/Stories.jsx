import AssetImage from '../common/AssetImage.jsx';
import { EmptyState, ErrorState, LoadingState } from '../common/ApiState.jsx';

export default function Stories({ state }) {
  if (state.loading) return <LoadingState label="Loading stories from API..." />;
  if (state.error) return <ErrorState error={state.error} />;
  const stories = Array.isArray(state.data) ? state.data : state.data?.items || [];
  if (!stories.length) return <EmptyState title="No stories yet" subtitle="GET /stories will populate this carousel." />;

  return (
    <div className="smart-stories">
      {stories.map((story) => (
        <article className="smart-story" key={story.id}>
          <AssetImage name={story.image || 'mobile_story_img.png'} />
          <strong>{story.user?.name || story.name}</strong>
        </article>
      ))}
    </div>
  );
}
