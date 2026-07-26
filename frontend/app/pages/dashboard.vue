<script setup lang="ts">
definePageMeta({ middleware: ['auth', 'verified'] })

const { user } = useAuth()
const [{ data: settingsData }, { data: taggingData }] = await Promise.all([
  useSiteSettings(),
  useTaggingOptions(),
])

const settings = computed(() => settingsData.value?.settings)
const peopleOptions = computed(() => taggingData.value?.people ?? [])
const siteTitle = computed(() => settings.value?.title ?? 'Family Tribute')

useSeoMeta({ title: () => `Dashboard | ${siteTitle.value}` })
</script>

<template>
  <div v-if="settings">
    <h1 class="font-header text-4xl text-hearthlight-deep dark:text-hearthlight">
      {{ siteTitle }}
    </h1>
    <p class="mt-4 text-2xl text-inkwell dark:text-aged-edge">
      Welcome to {{ siteTitle }}'s dashboard, {{ user?.name }}!
    </p>
    <p class="mt-4 text-faded-ink dark:text-old-binding">
      Here you can add new stories, photos, obituaries and more. We suggest starting by adding
      <a href="#new-person" class="font-bold text-hearthlight underline decoration-hearthlight underline-offset-1 transition hover:decoration-2">a person</a>
      first.
    </p>

    <div class="mt-10 space-y-14">
      <CreatePictureForm :people-options="peopleOptions" />
      <div class="h-px bg-hearthlight-subtle dark:bg-old-binding/30" />

      <CreateStoryForm :people-options="peopleOptions" />
      <div class="h-px bg-hearthlight-subtle dark:bg-old-binding/30" />

      <CreatePersonForm :people-options="peopleOptions" />
      <div class="h-px bg-hearthlight-subtle dark:bg-old-binding/30" />

      <SiteSettingsForm :settings="settings" />
    </div>
  </div>
</template>
