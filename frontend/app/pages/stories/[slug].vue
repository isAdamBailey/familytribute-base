<script setup lang="ts">
const route = useRoute()
const slug = route.params.slug as string

const [{ data: settings }, { data, error }] = await Promise.all([
  useSiteSettings(),
  useStory(slug),
])
throwIfNotFound(error.value, 'Story not found')

const story = computed(() => data.value!.story)
const isVideo = computed(() => !!story.value.media_url && /\.(mp4|webm|mov)$/i.test(story.value.media_url))
const siteTitle = computed(() => settings.value?.settings?.title ?? 'Family Tribute')
const canonicalUrl = useRequestURL().href

useSeoMeta({
  title: () => `${story.value.title} | ${siteTitle.value}`,
  description: () => story.value.excerpt,
  ogTitle: () => story.value.title,
  ogDescription: () => story.value.excerpt,
  ogType: 'article',
  ogUrl: canonicalUrl,
  twitterCard: 'summary',
  twitterTitle: () => story.value.title,
  twitterDescription: () => story.value.excerpt,
})
</script>

<template>
  <div>
    <div class="flex items-baseline justify-between gap-2">
      <h1 class="font-header text-5xl text-hearthlight-deep dark:text-hearthlight">{{ story.title }}</h1>
      <span v-if="story.year" class="shrink-0 text-lg text-faded-ink dark:text-old-binding">{{ story.year }}</span>
    </div>

    <blockquote class="mt-4 border-l-4 border-hearthlight pl-4 text-lg italic text-faded-ink dark:text-old-binding">
      {{ story.excerpt }}
    </blockquote>

    <video v-if="story.media_url && isVideo" :src="story.media_url" controls class="mt-6 w-full rounded-lg shadow-card" />
    <audio v-else-if="story.media_url" :src="story.media_url" controls class="mt-6 w-full" />

    <div class="html-content prose mt-6 max-w-2xl dark:prose-invert" v-html="story.content" />

    <RelatedPeople title="People in this story" :people="story.people ?? []" />
  </div>
</template>
