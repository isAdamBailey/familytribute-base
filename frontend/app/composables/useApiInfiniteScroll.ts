import type { Paginated } from '~/types/api'

/**
 * Client-side "load more on scroll" for a paginated API list, mirroring the
 * old Inertia app's useInfiniteScroll: an IntersectionObserver on a sentinel
 * element fetches `links.next` and appends its `data` to the accumulated list.
 *
 * `endpoint` is the API path (e.g. '/people'); `dataKey` is the response
 * envelope key that wraps the Paginated<T> (e.g. 'people'); `initial` is the
 * first page's Paginated<T> as already fetched (SSR-friendly) by the page's
 * own useFetch.
 */
export function useApiInfiniteScroll<T>(endpoint: string, dataKey: string, initial: Ref<Paginated<T> | undefined>) {
  const { $api } = useNuxtApp()

  const items = ref<T[]>([]) as Ref<T[]>
  const nextUrl = ref<string | null>(null)
  const loading = ref(false)
  const sentinel = ref<HTMLElement | null>(null)
  const hasMore = computed(() => !!nextUrl.value)

  watch(initial, (value) => {
    items.value = value?.data ?? []
    nextUrl.value = value?.links.next ?? null
  }, { immediate: true })

  async function loadMore() {
    if (loading.value || !nextUrl.value) return
    loading.value = true
    try {
      const query = Object.fromEntries(new URL(nextUrl.value).searchParams.entries())
      const page = await $api<Record<string, Paginated<T>>>(endpoint, { query })
      const paginated = page[dataKey]
      if (paginated) {
        items.value = [...items.value, ...paginated.data]
        nextUrl.value = paginated.links.next
      }
    } finally {
      loading.value = false
    }
  }

  let observer: IntersectionObserver | null = null

  onMounted(() => {
    observer = new IntersectionObserver((entries) => {
      if (entries.some((entry) => entry.isIntersecting)) loadMore()
    }, { rootMargin: '0px 0px 200px 0px' })

    // The sentinel is only rendered (`v-if="hasMore"`) once there's a next
    // page to load, so it doesn't exist yet at mount time on first load —
    // (re)attach the observer whenever the element itself appears/changes,
    // not just once at mount.
    watch(sentinel, (el, previousEl) => {
      if (previousEl) observer!.unobserve(previousEl)
      if (el) observer!.observe(el)
    }, { immediate: true })
  })

  onUnmounted(() => observer?.disconnect())

  return { items, sentinel, hasMore, loading }
}
