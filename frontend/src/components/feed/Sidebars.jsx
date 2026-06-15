import { Link } from 'react-router-dom';
import { CalendarDays, MessageCircle, UserRound, UsersRound } from 'lucide-react';
import AssetImage from '../common/AssetImage.jsx';
import { EmptyState, ErrorState, LoadingState } from '../common/ApiState.jsx';

export function LeftSidebar({ profile }) {
  return (
    <div className="smart-sidebar">
      <div className="smart-profile-card">
        <AssetImage name="profile-cover-img.png" className="smart-cover" />
        <AssetImage name={profile?.avatar || 'profile.png'} className="smart-avatar" />
        <h4>{profile?.name || 'Authenticated User'}</h4>
        <p>{profile?.headline || 'Profile information will come from /auth/me API.'}</p>
      </div>
      <nav className="smart-menu">
        <Link to="/profile"><UserRound size={18} /> Profile</Link>
        <Link to="/find-friends"><UsersRound size={18} /> Find Friends</Link>
        <Link to="/groups"><UsersRound size={18} /> Groups</Link>
        <Link to="/events"><CalendarDays size={18} /> Events</Link>
        <Link to="/chat"><MessageCircle size={18} /> Messages</Link>
      </nav>
    </div>
  );
}

export function RightSidebar({ suggestionsState, eventsState }) {
  return (
    <div className="smart-sidebar right">
      <section className="smart-card">
        <div className="smart-card-title"><h5>People You May Know</h5><Link to="/find-friends">See all</Link></div>
        <ApiList state={suggestionsState} type="people" />
      </section>
      <section className="smart-card">
        <div className="smart-card-title"><h5>Upcoming Events</h5><Link to="/events">See all</Link></div>
        <ApiList state={eventsState} type="events" />
      </section>
    </div>
  );
}

function ApiList({ state, type }) {
  if (state.loading) return <LoadingState label="Loading from API..." />;
  if (state.error) return <ErrorState error={state.error} />;
  const items = Array.isArray(state.data) ? state.data : state.data?.items || [];
  if (!items.length) return <EmptyState subtitle={`Connect ${type} endpoint to render this section.`} />;

  return (
    <div className="smart-list">
      {items.map((item) => (
        <div className="smart-list-item" key={item.id || item.email || item.title}>
          <AssetImage name={item.avatar || item.image || 'profile.png'} />
          <div><strong>{item.name || item.title}</strong><span>{item.subtitle || item.date || item.mutualFriends || ''}</span></div>
        </div>
      ))}
    </div>
  );
}
