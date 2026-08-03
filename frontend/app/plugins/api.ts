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

  const forwarded = serverForwardHeaders()

  const api = $fetch.create({
    baseURL,
    credentials: 'include',
    headers: {
      Accept: 'application/json',
    },
    onRequest({ options }) {
      const headers = new Headers(options.headers)

      if (forwarded.cookie) {
        headers.set('cookie', forwarded.cookie)
      }

      if (forwarded.origin) {
        headers.set('origin', forwarded.origin)
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
