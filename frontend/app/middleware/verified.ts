/**
 * Mirrors web.php's `verified` middleware on the equivalent Inertia routes —
 * kept separate from `auth` (rather than folded into it) so pages can compose
 * `middleware: ['auth', 'verified']` the same way the backend composes its
 * own middleware stack, instead of every "logged in" check also silently
 * requiring email verification.
 */
export default defineNuxtRouteMiddleware(() => {
  const { user } = useAuth()

  if (!user.value?.email_verified_at) {
    return navigateTo('/email/verify')
  }
})
