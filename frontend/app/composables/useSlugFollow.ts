/**
 * Editing a Picture/Story/Person's title regenerates its slug
 * (spatie/sluggable, on by default for updates), so a show page's URL must
 * follow it after a successful edit — calling `refresh()` alone would keep
 * fetching the now-stale old slug and 404 on the next action (e.g. delete).
 *
 * @param currentSlug the slug this page was loaded with
 * @param basePath the path prefix the resource lives under, e.g. '/pictures' (person show pages use '')
 * @param refresh the page's own resource refresh() (used when the slug didn't change)
 * @param editOpen the edit modal's open ref, closed once the update is handled
 */
export function useSlugFollow(
  currentSlug: string,
  basePath: string,
  refresh: () => Promise<unknown>,
  editOpen: Ref<boolean>,
) {
  return async function onUpdated(updated: { slug: string }) {
    editOpen.value = false
    if (updated.slug !== currentSlug) {
      await navigateTo(`${basePath}/${updated.slug}`, { replace: true })
    } else {
      await refresh()
    }
  }
}
