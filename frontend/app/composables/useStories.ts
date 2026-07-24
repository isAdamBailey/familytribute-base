import type { Paginated, Story } from '~/types/api'
import type { ListQuery } from '~/composables/useListPage'

/**
 * GET /api/stories → { stories: Paginated<Story>, sort, order, search }.
 */
export function useStories(query: Ref<ListQuery>) {
  return useApiFetch<{ stories: Paginated<Story>, sort: string, order: string, search: string | null }>('/stories', {
    query,
    key: computed(() => `stories-${JSON.stringify(query.value)}`),
  })
}

/**
 * GET /api/stories/{slug} → { story: Story, people: PersonRef[] } (allForTagging).
 * 404s (via Laravel `abort(404)`) when the story is private and the request is unauthenticated.
 */
export function useStory(slug: string) {
  return useApiFetch<{ story: Story }>(`/stories/${slug}`, {
    key: `story-${slug}`,
  })
}
