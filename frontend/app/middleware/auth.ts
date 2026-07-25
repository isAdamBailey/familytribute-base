export default defineNuxtRouteMiddleware(async () => {
  const { isLoggedIn, fetchUser } = useAuth()
  if (isLoggedIn.value) return

  // The auth plugin's own fetchUser() already ran once for this request; a
  // still-missing user here usually does mean logged-out, but retry once
  // before bouncing to the homepage — a transient fetch failure (network
  // blip, a slow response under a single-threaded dev server) shouldn't cost
  // a genuinely logged-in visitor their session.
  await fetchUser()
  if (!isLoggedIn.value) {
    return navigateTo('/')
  }
})
