import type { $Fetch } from 'nitropack'

/**
 * Provides `$api` — a configured $fetch instance for the Laravel JSON API.
 *
 * Handles the Sanctum SPA cookie contract:
 *  - `credentials: 'include'` so the session cookie rides along cross-origin.
 *  - On the server (SSR), forwards the incoming browser's `cookie` header to
 *    Laravel so privacy-scoped data resolves for the logged-in user.
 *  - On the client, mirrors the `XSRF-TOKEN` cookie into the `X-XSRF-TOKEN`
 *    header so state-changing requests (login/logout/CRUD) pass CSRF checks.
 *  - Uses `apiBaseServer` for internal SSR calls (direct to Laravel) and the
 *    public `apiBase` in the browser.
 */
export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()

  const baseURL = import.meta.server
    ? config.apiBaseServer || config.public.apiBase
    : config.public.apiBase

  // On the server, capture the incoming request's cookies to forward upstream.
  const requestHeaders = import.meta.server ? useRequestHeaders(['cookie']) : {}
  // Sanctum's EnsureFrontendRequestsAreStateful only treats a request as the
  // SPA (cookie-authenticated) rather than falling back to token auth when
  // its Referer/Origin header matches a configured stateful domain. A real
  // browser sends this automatically on cross-origin fetches, but Nuxt's SSR
  // requests are server-to-server (Node → Laravel) with no browser attached,
  // so without this the forwarded session cookie is silently ignored and
  // every SSR-rendered page (auth-gated routes, privacy-scoped content) sees
  // a logged-out request even when the visitor has a valid session.
  const origin = import.meta.server ? useRequestURL().origin : null

  const api = $fetch.create({
    baseURL,
    credentials: 'include',
    headers: {
      Accept: 'application/json',
    },
    onRequest({ options }) {
      const headers = new Headers(options.headers)

      if (import.meta.server && requestHeaders.cookie) {
        headers.set('cookie', requestHeaders.cookie)
      }

      if (import.meta.server && origin) {
        headers.set('origin', origin)
      }

      if (import.meta.client) {
        const token = getCookie('XSRF-TOKEN')
        if (token) {
          headers.set('X-XSRF-TOKEN', token)
        }
      }

      options.headers = headers
    },
  })

  return {
    provide: {
      api: api as $Fetch,
    },
  }
})

/** Read a cookie value in the browser (XSRF-TOKEN is URL-encoded by Laravel). */
function getCookie(name: string): string | null {
  const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'))
  return match ? decodeURIComponent(match[2]!) : null
}
