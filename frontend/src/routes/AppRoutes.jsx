import { Navigate, Route, Routes } from 'react-router-dom';
import Feed from '../pages/Feed.jsx';
import Login from '../pages/Login.jsx';
import Register from '../pages/Register.jsx';
import UnderConstructionPage from '../pages/UnderConstructionPage.jsx';
import NotFound from '../pages/NotFound.jsx';
import { authApi } from '../api/authApi';

const plannedRoutes = [
  '/profile',
  '/chat',
  '/friends',
  '/find-friends',
  '/groups',
  '/group',
  '/events',
  '/event',
  '/notifications',
  '/settings',
];

function isAuthenticated() {
  return authApi.isAuthenticated();
}

function ProtectedRoute({ children }) {
  if (!isAuthenticated()) return <Navigate to="/login" replace />;

  return children;
}

function PublicOnlyRoute({ children }) {
  if (isAuthenticated()) return <Navigate to="/feed" replace />;

  return children;
}

export default function AppRoutes() {
  return (
    <Routes>
      <Route path="/" element={<Navigate to={isAuthenticated() ? '/feed' : '/login'} replace />} />
      <Route
        path="/login"
        element={(
          <PublicOnlyRoute>
            <Login />
          </PublicOnlyRoute>
        )}
      />
      <Route
        path="/register"
        element={(
          <PublicOnlyRoute>
            <Register />
          </PublicOnlyRoute>
        )}
      />
      <Route
        path="/feed"
        element={(
          <ProtectedRoute>
            <Feed />
          </ProtectedRoute>
        )}
      />
      {plannedRoutes.map((path) => (
        <Route key={path} path={path} element={<UnderConstructionPage />} />
      ))}
      <Route path="/event/:id" element={<UnderConstructionPage />} />
      <Route path="*" element={<NotFound />} />
    </Routes>
  );
}
