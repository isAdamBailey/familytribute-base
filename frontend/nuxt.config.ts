// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-01-01',
  devtools: { enabled: true },

  // SSR is required so crawlers/social scrapers get server-rendered meta
  // tags — this replaces the Laravel-side meta package, removed at cutover
  // (issue #19 Phase 6).
  ssr: true,

  modules: [
    '@nuxt/eslint',
    '@nuxtjs/tailwindcss',
    '@nuxtjs/color-mode',
    '@nuxtjs/google-fonts',
    '@vueuse/nuxt',
  ],

  // Nuxt's default auto-import prefixes components in subfolders with the
  // directory name (components/dashboard/CreatePictureForm.vue would become
  // <DashboardCreatePictureForm>). Phase 4 organizes components into
  // dashboard/modals/profile subfolders for readability, so pathPrefix is
  // disabled to keep referencing them by their plain filename everywhere.
  components: [{ path: '~/components', pathPrefix: false }],

  // The Tailwind module owns CSS injection; point it at our entry (which holds
  // the @tailwind directives + ported @layer rules + RemixIcon import) so it
  // isn't double-injected.
  tailwindcss: {
    cssPath: '~/assets/css/main.css',
    configPath: '~/tailwind.config.ts',
    viewer: false,
  },

  // Class-based dark mode preserves the existing app's `dark:` utilities so
  // components ported in later phases keep working. Mirrors the Inertia app's
  // `darkMode: 'class'` + localStorage strategy.
  colorMode: {
    classSuffix: '',
    preference: 'system',
    fallback: 'light',
    storageKey: 'nuxt-color-mode',
  },

  googleFonts: {
    families: {
      // Body — fixes the "OpenSans" (no space) bug from the Inertia config.
      'Open+Sans': [400, 600, 700],
      // Display/headline script face.
      Gwendolyn: [700],
    },
    display: 'swap',
    // Self-host at build so we don't depend on Google's CDN at runtime.
    download: true,
  },

  // Production is single-origin (see ../DEPLOY.md): Nuxt and the Laravel API
  // are served from the same domain, with nginx routing /api and /sanctum to
  // PHP-FPM and everything else to this Node process. So in prod apiBase /
  // backendOrigin are just the site's own URL — set via NUXT_PUBLIC_API_BASE
  // / NUXT_PUBLIC_BACKEND_ORIGIN, no separate API host needed.
  runtimeConfig: {
    // Server-only: base the Nuxt SSR server uses to reach Laravel directly.
    // Falls back to the public base when unset.
    apiBaseServer: '',
    // Server-only: origin (no /api) the Nuxt SSR server uses for Sanctum's
    // CSRF-cookie route. Falls back to the public origin when unset.
    backendOriginServer: '',
    public: {
      // Browser-facing API base. Local dev default matches `sail up` (nginx on
      // :80, per CLAUDE.md) — not `php artisan serve`'s :8000.
      apiBase: 'http://localhost/api',
      // Origin (no /api) used for the Sanctum CSRF-cookie endpoint.
      backendOrigin: 'http://localhost',
      // GA4 measurement ID (NUXT_PUBLIC_GOOGLE_SITE_TAG). Empty by default, so
      // the gtag plugin no-ops everywhere it isn't configured; production sets
      // it per site (each has its own property) — see ../DEPLOY.md step 2.
      googleSiteTag: '',
    },
  },

  app: {
    head: {
      htmlAttrs: { lang: 'en' },
    },
  },
})
