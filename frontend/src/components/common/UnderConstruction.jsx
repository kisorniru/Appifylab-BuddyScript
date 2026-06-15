import { Link, useLocation } from 'react-router-dom';
import { Construction } from 'lucide-react';

export default function UnderConstruction() {
  const location = useLocation();
  return (
    <main className="construction-page _layout_main_wrapper">
      <section className="construction-card">
        <div className="construction-icon"><Construction size={44} /></div>
        <p className="construction-eyebrow">Planned Feature</p>
        <h1>Page Under Construction</h1>
        <p>
          The <strong>{location.pathname}</strong> module is intentionally routed to a polished fallback
          so the application has no broken links while the backend feature is being developed.
        </p>
        <div className="construction-actions">
          <Link to="/feed" className="btn btn-primary">Back to Feed</Link>
          <Link to="/login" className="btn btn-outline-secondary">Go to Login</Link>
        </div>
      </section>
    </main>
  );
}
