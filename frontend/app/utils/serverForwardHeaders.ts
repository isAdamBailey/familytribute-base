/**
 * Cookie + origin headers to forward when making a server-side (SSR) fetch to
 * Laravel. A Node→Laravel SSR call is server-to-server with no browser
 * attached, so without forwarding these explicitly, the incoming request's
 * session cookie is silently dropped (every SSR-rendered auth-gated or
 * privacy-scoped page would see a logged-out request) and Sanctum's
 * `EnsureFrontendRequestsAreStateful` — which keys off Origin/Referer to
 * decide whether to trust the cookie at all — has nothing to match against.
 *
 * Shared by plugins/api.ts's `$api` and useAuth.ts's `backendFetch`, the
 * app's two Laravel-facing fetch instances.
 */
export function serverForwardHeaders(): { cookie?: string, origin?: string } {
  if (import.meta.client) return {}

  const requestHeaders = useRequestHeaders(['cookie'])

  return {
    cookie: requestHeaders.cookie,
    origin: useRequestURL().origin,
  }
}
