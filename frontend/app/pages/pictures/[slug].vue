<script setup lang="ts">
const route = useRoute()
const slug = route.params.slug as string

const [{ data: settings }, { data, error }] = await Promise.all([
  useSiteSettings(),
  usePicture(slug),
])
throwIfNotFound(error.value, 'Picture not found')

const picture = computed(() => data.value!.picture)
const siteTitle = computed(() => settings.value?.settings?.title ?? 'Family Tribute')
const description = computed(() => stripHtml(picture.value.description))
const canonicalUrl = useRequestURL().href

useSeoMeta({
  title: () => `${picture.value.title} | ${siteTitle.value}`,
  description: () => description.value,
  ogTitle: () => picture.value.title,
  ogDescription: () => description.value,
  ogType: 'website',
  ogUrl: canonicalUrl,
  ogImage: () => picture.value.url,
  twitterCard: 'summary_large_image',
  twitterTitle: () => picture.value.title,
  twitterDescription: () => description.value,
  twitterImage: () => picture.value.url,
})
</script>

<template>
  <div>
    <img
      :src="picture.url"
      :alt="picture.title"
      class="mx-auto max-h-[70vh] w-full rounded-lg object-contain shadow-card"
    >

    <div class="mt-6 flex items-baseline justify-between gap-2">
      <h1 class="font-header text-5xl text-hearthlight-deep dark:text-hearthlight">{{ picture.title }}</h1>
      <span class="shrink-0 text-lg text-faded-ink dark:text-old-binding">{{ picture.year }}</span>
    </div>

    <div class="html-content prose mt-4 max-w-2xl dark:prose-invert" v-html="picture.description" />

    <RelatedPeople title="People in this photo" :people="picture.people ?? []" />
  </div>
</template>
