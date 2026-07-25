<script setup lang="ts">
const route = useRoute()
const slug = route.params.slug as string

const { isLoggedIn } = useAuth()
const [{ data: settings }, { data, error, refresh }] = await Promise.all([
  useSiteSettings(),
  usePicture(slug),
])
throwIfNotFound(error.value, 'Picture not found')

const picture = computed(() => data.value!.picture)
const peopleOptions = computed(() => data.value?.people ?? [])
const siteTitle = computed(() => settings.value?.settings?.title ?? 'Family Tribute')
const description = computed(() => stripHtml(picture.value.description))
const canonicalUrl = useRequestURL().href

const editOpen = ref(false)
const deleteOpen = ref(false)

async function onDeleted() {
  await navigateTo('/pictures')
}

const onUpdated = useSlugFollow(slug, '/pictures', refresh, editOpen)

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

    <div v-if="isLoggedIn" class="mt-4 flex gap-2">
      <button type="button" aria-label="Edit Picture" class="btn-secondary" @click="editOpen = true">
        Edit <i class="ri-edit-2-fill ml-1" />
      </button>
      <button type="button" aria-label="Delete Picture" class="btn-danger" @click="deleteOpen = true">
        <i class="ri-delete-bin-fill" />
      </button>
    </div>

    <div class="html-content prose mt-4 max-w-2xl dark:prose-invert" v-html="picture.description" />

    <RelatedPeople title="People in this photo" :people="picture.people ?? []" />

    <PictureEditModal
      v-if="isLoggedIn"
      :open="editOpen"
      :picture="picture"
      :people="peopleOptions"
      @close="editOpen = false"
      @updated="onUpdated"
    />
    <PictureDeleteModal
      v-if="isLoggedIn"
      :open="deleteOpen"
      :picture="picture"
      @close="deleteOpen = false"
      @deleted="onDeleted"
    />
  </div>
</template>
