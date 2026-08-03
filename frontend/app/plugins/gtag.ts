/**
 * Google Analytics (gtag.js).
 *
 * Ported from the Inertia app's resources/views/app.blade.php, which was
 * deleted wholesale at the Nuxt cutover (issue #19 Phase 6) — the analytics
 * snippet went with it and had no Nuxt equivalent. Same behaviour as before:
 * the tag only loads in production builds, and only when a measurement ID is
 * configured (NUXT_PUBLIC_GOOGLE_SITE_TAG, previously GOOGLE_SITE_TAG on the
 * Laravel side). Each site has its own GA property, so the ID is set per
 * daemon in production — see ../../../DEPLOY.md step 2.
 *
 * Universal (not .client) on purpose: with SSR on, useHead here puts the tag
 * in the server-rendered <head> so it loads with the document rather than
 * waiting for hydration.
 *
 * No manual page_view on route change — GA4's enhanced measurement tracks
 * history-based navigation itself, and firing our own would double-count.
 */
export default defineNuxtPlugin(() => {
  const siteTag = useRuntimeConfig().public.googleSiteTag

  // Dev traffic never reaches GA (the blade template's `@env('production')`).
  if (import.meta.dev || !siteTag) return

  useHead({
    script: [
      {
        src: `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(siteTag)}`,
        async: true,
      },
      {
        innerHTML: [
          'window.dataLayer = window.dataLayer || [];',
          'function gtag(){dataLayer.push(arguments);}',
          "gtag('js', new Date());",
          `gtag('config', ${JSON.stringify(siteTag)});`,
        ].join(''),
      },
    ],
  })
})
