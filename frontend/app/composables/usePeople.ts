import type { Paginated, Person } from '~/types/api'
import type { ListQuery } from '~/composables/useListPage'

/**
 * GET /api/people → { people: Paginated<Person>, sort, order, search }.
 */
export function usePeople(query: Ref<ListQuery>) {
  return useApiFetch<{ people: Paginated<Person>, sort: string, order: string, search: string | null }>('/people', {
    query,
    key: computed(() => `people-${JSON.stringify(query.value)}`),
  })
}

/**
 * GET /api/people/{slug} → { person: Person, people: PersonRef[] } (allForTagging).
 */
export function usePerson(slug: string) {
  return useApiFetch<{ person: Person }>(`/people/${slug}`, {
    key: `person-${slug}`,
  })
}
