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

// The mosaic's asymmetric layout pins specific picture slots (0-4) to
// specific grid positions/spans, so each tile needs its own placement class
// rather than a uniform v-for — this array is what maps slot index to class.
const mosaicSlots = [
  { index: 0, class: 'lg:col-span-2' },
  { index: 1, class: '' },
  { index: 4, class: 'lg:col-start-4 lg:row-span-2 lg:row-start-1' },
  { index: 2, class: 'col-span-2 lg:col-span-1 lg:row-start-2' },
  { index: 3, class: 'lg:col-span-2' },
]

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

    <section v-if="pictures.length" class="-mx-4 -mb-10 mt-14 sm:-mx-8">
      <div class="grid grid-cols-2 gap-0.5 lg:grid-cols-4">
        <template v-for="slot in mosaicSlots" :key="slot.index">
          <div v-if="pictures[slot.index]" class="group relative overflow-hidden" :class="slot.class">
            <img
              :src="pictures[slot.index]!.url"
              :alt="pictures[slot.index]!.title"
              loading="lazy"
              class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.03]"
            >
            <div
              v-if="pictures[slot.index]!.description"
              class="absolute inset-x-0 bottom-0 line-clamp-1 bg-hearthlight-deep/60 px-3 py-2.5 text-sm text-hearthlight-subtle backdrop-blur-sm"
            >
              {{ pictures[slot.index]!.description }}
            </div>
          </div>
        </template>
      </div>
    </section>
  </div>
</template>
