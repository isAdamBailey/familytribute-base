import type { User } from '~/types/api'

interface LoginCredentials {
  email: string
  password: string
  remember?: boolean
}

interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
  registration_secret: string
}

/**
 * Sanctum SPA cookie authentication against Laravel Fortify.
 *
 * Flow: csrf() primes the XSRF-TOKEN cookie, then login()/register() POST with
 * the X-XSRF-TOKEN header (added by plugins/api.ts) and Accept: application/json
 * so Fortify returns JSON. The resulting session cookie authenticates
 * subsequent auth:sanctum API calls.
 *
 * Auth endpoints (login/register/logout/csrf-cookie) live at the backend
 * ORIGIN, not under /api — so they use $fetch against backendOrigin directly,
 * still with credentials + the XSRF header.
 */
export function useAuth() {
  const config = useRuntimeConfig()
  const { $api } = useNuxtApp()

  const user = useState<User | null>('auth.user', () => null)
  const isLoggedIn = computed(() => user.value !== null)

  /** Read a browser cookie value (client-only). */
  function readCookie(name: string): string | null {
    if (import.meta.server) return null
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'))
    return match ? decodeURIComponent(match[2]!) : null
  }

  /** Fetch to the backend origin (for Fortify routes, which are not under /api). */
  function backendFetch<T>(path: string, options: Parameters<typeof $fetch>[1] = {}) {
    const headers = new Headers(options?.headers as HeadersInit)
    headers.set('Accept', 'application/json')
    if (import.meta.client) {
      const token = readCookie('XSRF-TOKEN')
      if (token) headers.set('X-XSRF-TOKEN', token)
    }
    return $fetch<T>(path, {
      baseURL: config.public.backendOrigin,
      credentials: 'include',
      ...options,
      headers,
    })
  }

  /** Prime the CSRF cookie before any state-changing auth request. */
  async function csrf() {
    await backendFetch('/sanctum/csrf-cookie')
  }

  async function login(credentials: LoginCredentials) {
    await csrf()
    await backendFetch('/login', { method: 'POST', body: credentials })
    await fetchUser()
  }

  async function register(payload: RegisterPayload) {
    await csrf()
    await backendFetch('/register', { method: 'POST', body: payload })
    await fetchUser()
  }

  async function logout() {
    await backendFetch('/logout', { method: 'POST' })
    user.value = null
  }

  /** Load the authenticated user, or null if the session is not valid. */
  async function fetchUser() {
    try {
      user.value = await $api<User>('/user')
    } catch {
      user.value = null
    }
    return user.value
  }

  return { user, isLoggedIn, csrf, login, register, logout, fetchUser }
}
