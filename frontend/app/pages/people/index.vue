<script setup lang="ts">
import type { Person } from '~/types/api'

const [{ data: settings }, { search, sort, order, items, sentinel, hasMore }] = await Promise.all([
  useSiteSettings(),
  useListPage<Person>({ endpoint: '/people', dataKey: 'people', fetch: usePeople }),
])

const siteTitle = computed(() => settings.value?.settings?.title ?? 'Family Tribute')
const canonicalUrl = useRequestURL().href

useSeoMeta({
  title: () => `People | ${siteTitle.value}`,
  description: 'Meet the family — the people we are remembering.',
  ogTitle: () => `People | ${siteTitle.value}`,
  ogType: 'website',
  ogUrl: canonicalUrl,
  twitterCard: 'summary',
  twitterTitle: () => `People | ${siteTitle.value}`,
})

const sortOptions = [
  { label: 'Last Name (Z-A)', sort: 'last_name', order: 'desc' },
  { label: 'Last Name (A-Z)', sort: 'last_name', order: 'asc' },
  { label: 'Death Date (newest)', sort: 'death_date', order: 'desc' },
  { label: 'Death Date (oldest)', sort: 'death_date', order: 'asc' },
]
</script>

<template>
  <div>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <h1 class="font-header text-5xl text-hearthlight-deep dark:text-hearthlight">People</h1>
      <div class="flex flex-nowrap items-center gap-2">
        <SearchInput v-model="search" placeholder="Search people…" />
        <SortSelect v-model:sort="sort" v-model:order="order" :options="sortOptions" />
      </div>
    </div>

    <div v-if="!items.length" class="mt-10 text-center text-faded-ink dark:text-old-binding">
      No one matches your search yet.
    </div>

    <div v-else class="mt-8 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
      <PersonCard v-for="person in items" :key="person.slug" :person="person" />
    </div>

    <div v-if="hasMore" ref="sentinel" class="h-10" />
  </div>
</template>
