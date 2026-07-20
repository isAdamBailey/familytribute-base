import type { UseFetchOptions } from 'nuxt/app'

/**
 * SSR-friendly data fetching against the Laravel JSON API.
 *
 * Thin wrapper over `useFetch` that routes through the `$api` instance from
 * plugins/api.ts (base URL, credentials, cookie-forwarding, XSRF header). Use
 * this for page-level reads that should render on the server; use `$api`
 * directly (via `useNuxtApp().$api`) for imperative calls like login.
 */
export function useApiFetch<T>(
  url: string | (() => string),
  options: UseFetchOptions<T> = {},
) {
  const { $api } = useNuxtApp()

  return useFetch(url, {
    ...options,
    $fetch: $api as typeof $fetch,
  })
}
