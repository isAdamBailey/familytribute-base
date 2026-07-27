import type { Paginated } from '~/types/api'

export interface ListQuery {
  search?: string
  sort?: string
  order?: string
}

interface UseListPageOptions {
  /** API path, e.g. '/people'. */
  endpoint: string
  /** Response envelope key wrapping the Paginated<T>, e.g. 'people'. */
  dataKey: string
  /** The resource composable, e.g. usePeople — called with the reactive query. */
  fetch: (query: Ref<ListQuery>) => Promise<{ data: Ref<Record<string, unknown> | null | undefined> }>
}

/**
 * Shared scaffolding for the People/Pictures/Stories index pages: search
 * (debounced) + sort/order synced to the URL query string, the first page
 * fetched SSR-friendly via the given resource composable, and infinite
 * scroll wired to append subsequent pages. Centralizing this means a fix to
 * the URL-sync or scroll behavior applies to all three list pages at once.
 */
export async function useListPage<T>({ endpoint, dataKey, fetch }: UseListPageOptions) {
  const route = useRoute()
  const router = useRouter()

  const search = ref((route.query.search as string) ?? '')
  const sort = ref((route.query.sort as string) ?? '')
  const order = ref((route.query.order as string) ?? '')
  const debouncedSearch = refDebounced(search, 300)

  const query = computed<ListQuery>(() => ({
    search: debouncedSearch.value,
    sort: sort.value,
    order: order.value,
  }))

  watch(query, (value) => {
    router.replace({ query: { ...value } })
  }, { deep: true })

  // Register infinite-scroll lifecycle hooks (onMounted / IntersectionObserver)
  // before awaiting the first page — Vue loses the active component instance
  // after the first await in an async composable, so hooks registered later
  // never attach and pagination silently stops working.
  const page = ref<Paginated<T> | undefined>()
  const { items, sentinel, hasMore } = useApiInfiniteScroll<T>(endpoint, dataKey, page)

  const { data } = await fetch(query)

  watch(data, (value) => {
    page.value = value?.[dataKey] as Paginated<T> | undefined
  }, { immediate: true })

  return { search, sort, order, items, sentinel, hasMore }
}
