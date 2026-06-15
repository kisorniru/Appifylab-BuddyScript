import { router } from "@inertiajs/react";

/**
 * Update query parameters and refresh the Inertia page.
 *
 * @param {Object} newParams - Key/value pairs of params to add or update.
 * @param {Object} options - Inertia visit options.
 */
export function updateQueryParams(newParams = {}, options = {}) {
  const currentUrl = new URL(window.location.href);
  const params = Object.fromEntries(currentUrl.searchParams.entries());
  const updatedParams = { ...params, ...newParams };

  Object.keys(updatedParams).forEach((key) => {
    if (updatedParams[key] === null || updatedParams[key] === "") {
      delete updatedParams[key];
    }
  });

  router.get(window.location.pathname, updatedParams, {
    preserveScroll: true,
    preserveState: true,
    ...options,
  });
}

/**
 * Get the query context
 * @returns URLSearchParams
 */
export function useQuery() {
  const url = new URL(window.location.href);

  return url.searchParams;
}
