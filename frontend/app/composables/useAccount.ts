export interface SessionInfo {
  agent: { is_desktop: boolean, platform: string | null, browser: string | null }
  ip_address: string | null
  is_current_device: boolean
  last_active: string
}

/**
 * Authenticated account management: profile info, password, 2FA, browser
 * sessions, account deletion. All of it — Fortify's profile/password/2FA
 * endpoints and this app's own AccountController endpoints alike — lives
 * under /api (config/fortify.php's prefix, issue #19 Phase 6), so it all
 * goes through the same `$api` instance (plugins/api.ts) as the rest of the
 * JSON API.
 */
export function useAccount() {
  const { fetchUser } = useAuth()
  const { $api } = useNuxtApp()

  async function updateProfileInformation(payload: { name: string, email: string }) {
    await $api('/user/profile-information', { method: 'PUT', body: payload })
    await fetchUser()
  }

  async function updatePassword(payload: { current_password: string, password: string, password_confirmation: string }) {
    await $api('/user/password', { method: 'PUT', body: payload })
  }

  async function confirmPassword(password: string) {
    await $api('/user/confirm-password', { method: 'POST', body: { password } })
  }

  async function enableTwoFactorAuthentication() {
    await $api('/user/two-factor-authentication', { method: 'POST' })
    await fetchUser()
  }

  async function disableTwoFactorAuthentication() {
    await $api('/user/two-factor-authentication', { method: 'DELETE' })
    await fetchUser()
  }

  function twoFactorQrCode() {
    return $api<{ svg: string, url: string }>('/user/two-factor-qr-code')
  }

  function twoFactorSecretKey() {
    return $api<{ secretKey: string }>('/user/two-factor-secret-key')
  }

  function twoFactorRecoveryCodes() {
    return $api<string[]>('/user/two-factor-recovery-codes')
  }

  async function regenerateRecoveryCodes() {
    await $api('/user/two-factor-recovery-codes', { method: 'POST' })
  }

  function sessions() {
    return $api<{ sessions: SessionInfo[] }>('/user/sessions')
  }

  async function logoutOtherBrowserSessions(password: string) {
    await $api('/user/other-browser-sessions', { method: 'DELETE', body: { password } })
  }

  async function deleteAccount(password: string) {
    await $api<{ status: string }>('/user', { method: 'DELETE', body: { password } })
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
