import type { SiteSettingsResponse } from '~/types/api'

/**
 * GET /api/site-settings → SiteSettingsResponse.
 *
 * Backed by useState (not useFetch/useAsyncData) so every call site — the
 * layout's header/footer title, each page's SEO meta, the dashboard's site
 * settings form — shares the exact same reactive ref. useFetch's per-call-site
 * key-based caching looked like it should do this too, but in practice a
 * refresh() from one call site didn't reliably propagate to others (e.g. the
 * layout's title staying stale after the dashboard form saved a new title);
 * useState's cross-component sharing is the documented, unambiguous
 * guarantee, and is what makes that save show up everywhere at once.
 *
 * Fetches once (first caller wins, SSR or client) and reuses the shared
 * state thereafter — call `refresh()` after a mutation to update it (and
 * every other consumer) in place.
 */
export async function useSiteSettings() {
  const data = useState<SiteSettingsResponse | null>('site-settings', () => null)
  const nuxtApp = useNuxtApp()

  async function refresh() {
    data.value = await nuxtApp.$api<SiteSettingsResponse>('/site-settings')
  }

  if (data.value === null) {
    // Stash the in-flight promise on the current (request-scoped) nuxtApp
    // instance so concurrent first-callers — e.g. the layout and a page both
    // rendering in the same SSR pass — await the same request instead of
    // each firing their own. A plain module-level variable would leak across
    // different users' SSR requests, since Nitro reuses the module instance
    // for the life of the server process.
    nuxtApp._siteSettingsFetch ??= refresh().finally(() => { delete nuxtApp._siteSettingsFetch })
    await nuxtApp._siteSettingsFetch
  }

  return { data, refresh }
}
