import type { Paginated, Person, TaggingPerson } from '~/types/api'
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
  return useApiFetch<{ person: Person, people: TaggingPerson[] }>(`/people/${slug}`, {
    key: `person-${slug}`,
  })
}

/**
 * GET /api/people/tagging → { people: TaggingPerson[] } (allForTagging, no
 * resource context). Used by the Dashboard's create forms. Auth-only —
 * resolves to an empty array for guests.
 */
export function useTaggingOptions() {
  return useApiFetch<{ people: TaggingPerson[] }>('/people/tagging', {
    key: 'people-tagging',
  })
}
