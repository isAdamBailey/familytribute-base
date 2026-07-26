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
 * Fortify's endpoints (login/register/logout/password/2FA/verification) live
 * under the same /api base as the rest of the JSON API — Fortify is prefixed
 * in config/fortify.php so this app and Nuxt can share one origin in
 * production (issue #19 Phase 6) — so they go through the same `$api`
 * instance (plugins/api.ts) as every other API call. Only Sanctum's
 * CSRF-cookie route sits at the bare backend origin, so `csrf()` below fetches
 * it directly instead.
 */
export function useAuth() {
  const config = useRuntimeConfig()
  const { $api } = useNuxtApp()

  const user = useState<User | null>('auth.user', () => null)
  const isLoggedIn = computed(() => user.value !== null)

  /**
   * Prime the CSRF cookie before any state-changing auth request.
   *
   * Mirrors `$api`'s header/cookie-forwarding contract (plugins/api.ts) but
   * against the bare backend origin rather than the /api base, since
   * sanctum/csrf-cookie isn't part of the JSON API.
   */
  async function csrf() {
    const headers = new Headers({ Accept: 'application/json' })
    const forwarded = serverForwardHeaders()
    if (forwarded.cookie) headers.set('cookie', forwarded.cookie)
    if (forwarded.origin) headers.set('origin', forwarded.origin)
    const baseURL = import.meta.server
      ? config.backendOriginServer || config.public.backendOrigin
      : config.public.backendOrigin
    await $fetch('/sanctum/csrf-cookie', { baseURL, credentials: 'include', headers })
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
    const response = await $api<{ two_factor?: boolean }>('/login', { method: 'POST', body: credentials })
    if (response?.two_factor) {
      return { twoFactor: true }
    }
    await fetchUser()
    return { twoFactor: false }
  }

  async function twoFactorChallenge(payload: TwoFactorChallengePayload) {
    await $api('/two-factor-challenge', { method: 'POST', body: payload })
    await fetchUser()
  }

  async function register(payload: RegisterPayload) {
    await csrf()
    await $api('/register', { method: 'POST', body: payload })
    await fetchUser()
  }

  async function logout() {
    await $api('/logout', { method: 'POST' })
    user.value = null
  }

  async function forgotPassword(email: string) {
    await csrf()
    return $api<{ status: string }>('/forgot-password', { method: 'POST', body: { email } })
  }

  async function resetPassword(payload: ResetPasswordPayload) {
    await csrf()
    return $api<{ status?: string }>('/reset-password', { method: 'POST', body: payload })
  }

  function resendVerificationEmail() {
    return $api<{ status: string }>('/email/verification-notification', { method: 'POST' })
  }

  /** Replays a verification email's id/hash/expires/signature against Fortify's signed route. */
  function verifyEmail(id: string, hash: string, query: Record<string, string>) {
    const qs = new URLSearchParams(query).toString()
    return $api(`/email/verify/${id}/${hash}${qs ? `?${qs}` : ''}`)
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
    twoFactorChallenge,
    forgotPassword,
    resetPassword,
    resendVerificationEmail,
    verifyEmail,
  }
}
