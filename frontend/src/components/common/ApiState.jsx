export function LoadingState({ label = 'Loading data from API...' }) {
  return <div className="api-state">{label}</div>;
}

export function EmptyState({ title = 'No data available', subtitle = 'Connect your backend API to populate this section.' }) {
  return (
    <div className="api-state api-state-empty">
      <strong>{title}</strong>
      <span>{subtitle}</span>
    </div>
  );
}

export function ErrorState({ error }) {
  return (
    <div className="api-state api-state-error">
      <strong>API connection pending</strong>
      <span>{error?.message || 'Backend endpoint is not available yet.'}</span>
    </div>
  );
}
