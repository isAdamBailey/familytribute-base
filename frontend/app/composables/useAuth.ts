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

interface ResetPasswordPayload {
  token: string
  email: string
  password: string
  password_confirmation: string
}

interface TwoFactorChallengePayload {
  code?: string
  recovery_code?: string
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

  /**
   * Fetch to the backend origin (for Fortify routes, which are not under /api).
   *
   * Only called client-side historically (form submits fire after hydration),
   * but the email-verification click-through page (/email/verify/[id]/[hash])
   * calls this during its initial SSR render too — `serverForwardHeaders()`
   * (shared with plugins/api.ts's `$api`) is what makes that SSR call carry
   * the visitor's session, since `credentials: 'include'` is a browser fetch
   * concept and does nothing server-side.
   */
  function backendFetch<T>(path: string, options: Parameters<typeof $fetch>[1] = {}) {
    const headers = new Headers(options?.headers as HeadersInit)
    headers.set('Accept', 'application/json')
    if (import.meta.client) {
      const token = readCookie('XSRF-TOKEN')
      if (token) headers.set('X-XSRF-TOKEN', token)
    }
    const forwarded = serverForwardHeaders()
    if (forwarded.cookie) headers.set('cookie', forwarded.cookie)
    if (forwarded.origin) headers.set('origin', forwarded.origin)
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

  /**
   * Returns `{ twoFactor: true }` without fetching the user when the account
   * has 2FA enabled — Fortify's `LoginResponse` establishes a pending
   * "two-factor" session (still a guest) rather than a real one in that case,
   * so the caller must route to the two-factor-challenge page next instead
   * of treating this as a completed login.
   */
  async function login(credentials: LoginCredentials) {
    await csrf()
    const response = await backendFetch<{ two_factor?: boolean }>('/login', { method: 'POST', body: credentials })
    if (response?.two_factor) {
      return { twoFactor: true }
    }
    await fetchUser()
    return { twoFactor: false }
  }

  async function twoFactorChallenge(payload: TwoFactorChallengePayload) {
    await backendFetch('/two-factor-challenge', { method: 'POST', body: payload })
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

  async function forgotPassword(email: string) {
    await csrf()
    return backendFetch<{ status: string }>('/forgot-password', { method: 'POST', body: { email } })
  }

  async function resetPassword(payload: ResetPasswordPayload) {
    await csrf()
    return backendFetch<{ status?: string }>('/reset-password', { method: 'POST', body: payload })
  }

  function resendVerificationEmail() {
    return backendFetch<{ status: string }>('/email/verification-notification', { method: 'POST' })
  }

  /** Replays a verification email's id/hash/expires/signature against Fortify's signed route. */
  function verifyEmail(id: string, hash: string, query: Record<string, string>) {
    const qs = new URLSearchParams(query).toString()
    return backendFetch(`/email/verify/${id}/${hash}${qs ? `?${qs}` : ''}`)
  }

  /**
   * Load the authenticated user, or null if the session is not valid.
   *
   * Only a 401 means "not logged in" — any other failure (network blip, 5xx,
   * a slow response timing out under `php artisan serve`'s single-threaded
   * dev server during e2e runs) leaves the current `user` state untouched
   * rather than treating a transient error as a real logout.
   */
  async function fetchUser() {
    try {
      user.value = await $api<User>('/user')
    } catch (error) {
      if (getStatusCode(error) === 401) {
        user.value = null
      }
    }
    return user.value
  }

  return {
    user,
    isLoggedIn,
    csrf,
    login,
    register,
    logout,
    fetchUser,
    backendFetch,
    twoFactorChallenge,
    forgotPassword,
    resetPassword,
    resendVerificationEmail,
    verifyEmail,
  }
}
