import { Link } from 'react-router-dom';
export default function NotFound() {
  return <main className="construction-page"><section className="construction-card"><h1>404</h1><p>This route does not exist.</p><Link to="/feed" className="btn btn-primary">Back to Feed</Link></section></main>;
}
