<script setup lang="ts">
import type { Story } from '~/types/api'

const [{ data: settings }, { search, sort, order, items, sentinel, hasMore }] = await Promise.all([
  useSiteSettings(),
  useListPage<Story>({ endpoint: '/stories', dataKey: 'stories', fetch: useStories }),
])

const siteTitle = computed(() => settings.value?.settings?.title ?? 'Family Tribute')
const canonicalUrl = useRequestURL().href

useSeoMeta({
  title: () => `Stories | ${siteTitle.value}`,
  description: 'Stories from our family history.',
  ogTitle: () => `Stories | ${siteTitle.value}`,
  ogType: 'website',
  ogUrl: canonicalUrl,
  twitterCard: 'summary',
  twitterTitle: () => `Stories | ${siteTitle.value}`,
})

const sortOptions = [
  { label: 'Year (newest)', sort: 'year', order: 'desc' },
  { label: 'Year (oldest)', sort: 'year', order: 'asc' },
]
</script>

<template>
  <div>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <h1 class="font-header text-5xl text-hearthlight-deep dark:text-hearthlight">Stories</h1>
      <div class="flex flex-nowrap items-center gap-2">
        <SearchInput v-model="search" placeholder="Search stories…" />
        <SortSelect v-model:sort="sort" v-model:order="order" :options="sortOptions" />
      </div>
    </div>

    <div v-if="!items.length" class="mt-10 text-center text-faded-ink dark:text-old-binding">
      No stories match your search yet.
    </div>

    <div v-else class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
      <StoryCard v-for="story in items" :key="story.slug" :story="story" />
    </div>

    <div v-if="hasMore" ref="sentinel" class="h-10" />
  </div>
</template>
