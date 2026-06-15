import { useEffect, useRef } from 'react';

export function useIntersectionLoadMore({ enabled, onLoadMore, rootMargin = '500px' }) {
  const targetRef = useRef(null);

  useEffect(() => {
    const target = targetRef.current;
    if (!target || !enabled) return undefined;

    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) onLoadMore();
      },
      { root: null, rootMargin, threshold: 0.1 }
    );

    observer.observe(target);
    return () => observer.disconnect();
  }, [enabled, onLoadMore, rootMargin]);

  return targetRef;
}
