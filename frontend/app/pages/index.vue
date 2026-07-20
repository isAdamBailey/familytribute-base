<script setup lang="ts">
// Phase 2 walking skeleton: proves the full pipeline (SSR data fetch from the
// Laravel JSON API, Tailwind tokens, fonts, SEO meta) end-to-end and gives the
// Playwright Home specs a real Nuxt target.
const { data: settings } = await useSiteSettings()
const { data: home } = await useHome()

const siteTitle = computed(() => settings.value?.settings?.title ?? 'Family Tribute')
// Site description is stored as rich-text HTML; use plain text for the meta
// description and the hero subtitle.
const siteDescription = computed(
  () => stripHtml(settings.value?.settings?.description) || 'The history of our family.',
)
const pictures = computed(() => home.value?.pictures ?? [])

// Resolve the request URL during setup (calling useRequestURL() lazily inside
// a useSeoMeta getter runs outside the Nuxt context and throws NUXT_E1001).
const canonicalUrl = useRequestURL().href

// Nuxt owns SEO from here on — server-rendered so crawlers/scrapers see real
// tags. This replaces HasSeoTags / the Laravel meta package at cutover.
useSeoMeta({
  title: () => `Home | ${siteTitle.value}`,
  description: () => siteDescription.value,
  ogTitle: () => `Home | ${siteTitle.value}`,
  ogDescription: () => siteDescription.value,
  ogType: 'website',
  ogUrl: canonicalUrl,
  twitterCard: 'summary',
  twitterTitle: () => `Home | ${siteTitle.value}`,
  twitterDescription: () => siteDescription.value,
})
</script>

<template>
  <div>
    <section class="text-center">
      <h1 class="font-header text-6xl text-hearthlight-deep dark:text-hearthlight sm:text-7xl">
        {{ siteTitle }}
      </h1>
      <p class="mx-auto mt-4 max-w-2xl text-lg text-faded-ink dark:text-aged-edge">
        {{ siteDescription }}
      </p>

      <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
        <NuxtLink
          to="/people"
          data-testid="cta-people"
          class="rounded-md bg-hearthlight px-6 py-3 font-semibold text-white shadow-card transition hover:bg-hearthlight-deep hover:shadow-button-hover"
        >
          Meet the Family
        </NuxtLink>
        <NuxtLink
          to="/pictures"
          data-testid="cta-pictures"
          class="rounded-md border border-hearthlight px-6 py-3 font-semibold text-hearthlight-deep transition hover:bg-hearthlight-subtle dark:text-hearthlight"
        >
          View Pictures
        </NuxtLink>
      </div>
    </section>

    <section v-if="pictures.length" class="mt-16">
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <figure
          v-for="picture in pictures"
          :key="picture.url"
          class="overflow-hidden rounded-lg bg-white shadow-card transition hover:shadow-card-hover dark:bg-inkwell/60"
        >
          <img
            :src="picture.url"
            :alt="picture.title"
            class="aspect-[4/3] w-full object-cover"
            loading="lazy"
          >
          <figcaption class="card-text-gradient px-4 py-3 dark:bg-none">
            <p class="font-semibold text-inkwell dark:text-aged-edge">{{ picture.title }}</p>
            <p v-if="picture.description" class="mt-1 line-clamp-2 text-sm text-faded-ink dark:text-old-binding">
              {{ picture.description }}
            </p>
          </figcaption>
        </figure>
      </div>
    </section>
  </div>
</template>
