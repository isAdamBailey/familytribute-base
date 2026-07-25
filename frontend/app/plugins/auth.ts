/**
 * Hydrates auth.user on every SSR request (so the first render already knows
 * whether the visitor is logged in). Session cookies are forwarded to
 * Laravel by the `$api` plugin, so this just resolves whether that session
 * is valid.
 *
 * On the client, `user` is a useState and Nuxt already restores it from the
 * SSR payload before plugins run — re-fetching unconditionally would fire a
 * second, redundant `/api/user` request on every page view. Only fetch
 * client-side when hydration left it empty (SSR was skipped entirely, e.g.
 * a client-only navigation into the app).
 */
export default defineNuxtPlugin(async () => {
  const { user, fetchUser } = useAuth()
  if (import.meta.server || user.value === null) {
    await fetchUser()
  }
})
