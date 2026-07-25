/**
 * Global flash-message state, shown via <FlashBanner> in the default layout.
 * Nuxt has no server-side flash-via-redirect like Inertia did, so CRUD
 * mutations call `flash()` client-side right after a successful request.
 */
export function useFlash() {
  const message = useState<string | null>('flash.message', () => null)

  function flash(text: string) {
    message.value = text
  }

  function clear() {
    message.value = null
  }

  return { message, flash, clear }
}
