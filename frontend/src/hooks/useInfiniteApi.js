import { useCallback, useEffect, useRef, useState } from 'react';

export function useInfiniteApi(loader, { limit = 10, enabled = true } = {}) {
  const [items, setItems] = useState([]);
  const [cursor, setCursor] = useState(null);
  const [hasMore, setHasMore] = useState(true);
  const [loading, setLoading] = useState(false);
  const [initialLoading, setInitialLoading] = useState(true);
  const [error, setError] = useState(null);
  const requestInFlight = useRef(false);

  const loadMore = useCallback(async () => {
    if (!enabled || requestInFlight.current || !hasMore) return;

    requestInFlight.current = true;
    setLoading(true);
    setError(null);

    try {
      const response = await loader({ cursor, limit });
      const nextItems = response?.items || [];
      const meta = response?.meta || {};

      setItems((current) => {
        const seen = new Set(current.map((item) => item.id));
        const uniqueNextItems = nextItems.filter((item) => !seen.has(item.id));
        return [...current, ...uniqueNextItems];
      });

      setCursor(meta.next_cursor || null);
      setHasMore(Boolean(meta.has_more && meta.next_cursor));
    } catch (err) {
      setError(err);
    } finally {
      requestInFlight.current = false;
      setLoading(false);
      setInitialLoading(false);
    }
  }, [cursor, enabled, hasMore, limit, loader]);

  const refresh = useCallback(async () => {
    setItems([]);
    setCursor(null);
    setHasMore(true);
    setInitialLoading(true);
  }, []);

  useEffect(() => {
    if (enabled && initialLoading && items.length === 0) loadMore();
  }, [enabled, initialLoading, items.length, loadMore]);

  return { items, loading, initialLoading, error, hasMore, loadMore, refresh };
}
