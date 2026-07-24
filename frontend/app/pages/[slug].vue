<script setup lang="ts">
const route = useRoute()
const slug = route.params.slug as string

const [{ data: settings }, { data, error }] = await Promise.all([
  useSiteSettings(),
  usePerson(slug),
])
throwIfNotFound(error.value, 'Person not found')

const person = computed(() => data.value!.person)
const obituary = computed(() => person.value.obituary)
const siteTitle = computed(() => settings.value?.settings?.title ?? 'Family Tribute')
const description = computed(() => stripHtml(obituary.value?.content) || siteTitle.value)
const canonicalUrl = useRequestURL().href

useSeoMeta({
  title: () => `${person.value.full_name} | ${siteTitle.value}`,
  description: () => description.value,
  ogTitle: () => person.value.full_name,
  ogDescription: () => description.value,
  ogType: 'profile',
  ogUrl: canonicalUrl,
  ogImage: () => person.value.photo_url,
  twitterCard: 'summary_large_image',
  twitterTitle: () => person.value.full_name,
  twitterDescription: () => description.value,
  twitterImage: () => person.value.photo_url,
})
</script>

<template>
  <div>
    <!-- Hero -->
    <div class="-mx-4 -mt-10 sm:-mx-8">
      <img
        v-if="obituary?.background_photo_url"
        :src="obituary.background_photo_url"
        :alt="`${person.full_name} background photo`"
        class="h-72 w-full object-cover"
      >
      <div v-else class="h-44 bg-gradient-to-br from-hearthlight-subtle via-white to-hearthlight-subtle dark:from-inkwell dark:via-inkwell/80 dark:to-inkwell" />
    </div>

    <!-- Avatar -->
    <div class="-mt-20 flex justify-center">
      <img
        :src="person.photo_url"
        :alt="person.full_name"
        class="relative z-10 h-40 w-40 rounded-full border-4 border-white object-cover shadow-card dark:border-inkwell"
      >
    </div>

    <div class="mt-4 text-center">
      <h1 class="font-header text-6xl text-hearthlight-deep dark:text-hearthlight">
        {{ person.full_name }}
      </h1>
      <p v-if="obituary" class="mt-2 text-sm font-semibold text-faded-ink dark:text-old-binding">
        {{ formatDate(obituary.birth_date) }} – {{ formatDate(obituary.death_date) }}
      </p>
    </div>

    <div class="my-10 flex items-center gap-3">
      <div class="h-px flex-1 bg-hearthlight-subtle dark:bg-old-binding/30" />
      <div class="h-1.5 w-1.5 rounded-full bg-hearthlight/40" />
      <div class="h-px w-8 bg-hearthlight/30" />
      <div class="h-1.5 w-1.5 rounded-full bg-hearthlight/40" />
      <div class="h-px flex-1 bg-hearthlight-subtle dark:bg-old-binding/30" />
    </div>

    <div v-if="obituary" class="mx-auto max-w-2xl">
      <h2 class="font-header text-3xl text-hearthlight-deep dark:text-hearthlight">Obituary</h2>
      <div class="html-content prose mt-6 max-w-none dark:prose-invert" v-html="obituary.content" />
    </div>

    <section v-if="person.pictures?.length" class="mt-14">
      <h2 class="font-header text-3xl text-hearthlight-deep dark:text-hearthlight">Pictures of {{ person.first_name }}</h2>
      <div class="mt-4 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
        <PictureCard v-for="picture in person.pictures" :key="picture.slug" :picture="picture" />
      </div>
    </section>

    <section v-if="person.stories?.length" class="mt-14">
      <h2 class="font-header text-3xl text-hearthlight-deep dark:text-hearthlight">Memories of {{ person.first_name }}</h2>
      <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <StoryCard v-for="story in person.stories" :key="story.slug" :story="story" />
      </div>
    </section>

    <RelatedPeople title="Parents" :people="person.parents ?? []" />
    <RelatedPeople title="Children" :people="person.children ?? []" />
  </div>
</template>
