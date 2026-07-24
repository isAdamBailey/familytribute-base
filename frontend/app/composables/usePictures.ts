import type { Paginated, Picture } from '~/types/api'
import type { ListQuery } from '~/composables/useListPage'

/**
 * GET /api/pictures → { pictures: Paginated<Picture>, sort, order, search }.
 */
export function usePictures(query: Ref<ListQuery>) {
  return useApiFetch<{ pictures: Paginated<Picture>, sort: string, order: string, search: string | null }>('/pictures', {
    query,
    key: computed(() => `pictures-${JSON.stringify(query.value)}`),
  })
}

/**
 * GET /api/pictures/{slug} → { picture: Picture, people: PersonRef[] } (allForTagging).
 * 404s (via Laravel `abort(404)`) when the picture is private and the request is unauthenticated.
 */
export function usePicture(slug: string) {
  return useApiFetch<{ picture: Picture }>(`/pictures/${slug}`, {
    key: `picture-${slug}`,
  })
}
