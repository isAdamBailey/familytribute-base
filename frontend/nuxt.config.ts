// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-01-01',
  devtools: { enabled: true },

  // SSR is required so crawlers/social scrapers get server-rendered meta
  // tags (this replaces the Laravel-side meta package at cutover — Phase 6).
  ssr: true,

  modules: [
    '@nuxt/eslint',
    '@nuxtjs/tailwindcss',
    '@nuxtjs/color-mode',
    '@nuxtjs/google-fonts',
    '@vueuse/nuxt',
  ],

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

  runtimeConfig: {
    // Server-only: base the Nuxt SSR server uses to reach Laravel directly.
    // Falls back to the public base when unset. In prod → https://api.familytribute.x/api
    apiBaseServer: '',
    public: {
      // Browser-facing API base. In prod → https://api.familytribute.x/api
      apiBase: 'http://localhost:8000/api',
      // Origin (no /api) used for the Sanctum CSRF-cookie endpoint.
      backendOrigin: 'http://localhost:8000',
    },
  },

  app: {
    head: {
      htmlAttrs: { lang: 'en' },
    },
  },
})
