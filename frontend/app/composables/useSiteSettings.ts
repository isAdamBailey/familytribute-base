import type { SiteSettings } from '~/types/api'

/**
 * GET /api/site-settings → { settings: SiteSettings | null }.
 * Shared across pages via a stable useFetch key so it is fetched once per
 * navigation. Provides site title/description used for layout + SEO.
 */
export function useSiteSettings() {
  return useApiFetch<{ settings: SiteSettings | null }>('/site-settings', {
    key: 'site-settings',
    getCachedData(key, nuxtApp) {
      // Reuse the payload across the app; refetch only on a hard reload.
      return nuxtApp.payload.data[key] ?? nuxtApp.static.data[key]
    },
  })
}
