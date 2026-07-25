import type { User } from '~/types/api'

export interface SessionInfo {
  agent: { is_desktop: boolean, platform: string | null, browser: string | null }
  ip_address: string | null
  is_current_device: boolean
  last_active: string
}

/**
 * Authenticated account management: profile info, password, 2FA, browser
 * sessions, account deletion.
 *
 * Fortify's profile/password/2FA endpoints are `wantsJson()`-aware and live
 * at the backend origin (not /api), so they go through useAuth's
 * `backendFetch`. Sessions listing / logout-other-sessions / delete-account
 * are custom JSON endpoints added under /api/user/* (Jetstream's originals
 * only render Inertia), so those use `$api` like the rest of the JSON API.
 */
export function useAccount() {
  const { backendFetch, fetchUser } = useAuth()
  const { $api } = useNuxtApp()

  async function updateProfileInformation(payload: { name: string, email: string }) {
    await backendFetch('/user/profile-information', { method: 'PUT', body: payload })
    await fetchUser()
  }

  async function updatePassword(payload: { current_password: string, password: string, password_confirmation: string }) {
    await backendFetch('/user/password', { method: 'PUT', body: payload })
  }

  async function confirmPassword(password: string) {
    await backendFetch('/user/confirm-password', { method: 'POST', body: { password } })
  }

  async function enableTwoFactorAuthentication() {
    await backendFetch('/user/two-factor-authentication', { method: 'POST' })
    await fetchUser()
  }

  async function disableTwoFactorAuthentication() {
    await backendFetch('/user/two-factor-authentication', { method: 'DELETE' })
    await fetchUser()
  }

  function twoFactorQrCode() {
    return backendFetch<{ svg: string, url: string }>('/user/two-factor-qr-code')
  }

  function twoFactorSecretKey() {
    return backendFetch<{ secretKey: string }>('/user/two-factor-secret-key')
  }

  function twoFactorRecoveryCodes() {
    return backendFetch<string[]>('/user/two-factor-recovery-codes')
  }

  async function regenerateRecoveryCodes() {
    await backendFetch('/user/two-factor-recovery-codes', { method: 'POST' })
  }

  function sessions() {
    return $api<{ sessions: SessionInfo[] }>('/user/sessions')
  }

  async function logoutOtherBrowserSessions(password: string) {
    await $api('/user/other-browser-sessions', { method: 'DELETE', body: { password } })
  }

  async function deleteAccount(password: string) {
    await $api<User>('/user', { method: 'DELETE', body: { password } })
  }

  return {
    updateProfileInformation,
    updatePassword,
    confirmPassword,
    enableTwoFactorAuthentication,
    disableTwoFactorAuthentication,
    twoFactorQrCode,
    twoFactorSecretKey,
    twoFactorRecoveryCodes,
    regenerateRecoveryCodes,
    sessions,
    logoutOtherBrowserSessions,
    deleteAccount,
  }
}
