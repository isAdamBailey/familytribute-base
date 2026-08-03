/**
 * Google Analytics (gtag.js).
 *
 * Ported from the Inertia app's resources/views/app.blade.php, which was
 * deleted wholesale at the Nuxt cutover (issue #19 Phase 6) — the analytics
 * snippet went with it and had no Nuxt equivalent.
 *
 * The measurement ID still comes from each site's Laravel .env
 * (GOOGLE_SITE_TAG), exactly as it did before: Nuxt runs as a separate Forge
 * daemon that never loads that .env, so the API hands the ID over on
 * GET /api/site-settings — a request every page already makes, shared through
 * useSiteSettings' useState, so this costs no extra round-trip. Laravel only
 * sends it when APP_ENV=production, which is where the old
 * `@env('production')` guard now lives.
 *
 * Universal (not .client) on purpose: with SSR on, useHead here puts the tag
 * in the server-rendered <head> so it loads with the document rather than
 * waiting for hydration.
 *
 * No manual page_view on route change — GA4's enhanced measurement tracks
 * history-based navigation itself, and firing our own would double-count.
 */
export default defineNuxtPlugin({
  name: 'gtag',
  // useSiteSettings calls $api, which the api plugin provides. Filename order
  // happens to get this right today; saying so explicitly keeps a rename from
  // silently breaking it.
  dependsOn: ['api'],

  async setup() {
    // Belt and braces alongside the API's production check: a dev server
    // pointed at the production API still must not report page views.
    if (import.meta.dev) return

    let siteTag: string | null

    try {
      const { data } = await useSiteSettings()
      siteTag = data.value?.google_site_tag ?? null
    }
    catch {
      // Analytics must never take the page down. If site-settings failed, the
      // layout's own call will surface the error; this plugin stays quiet.
      return
    }

    if (!siteTag) return

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
  },
})
