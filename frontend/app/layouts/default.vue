<script setup lang="ts">
const { data: settings } = await useSiteSettings()
const siteTitle = computed(() => settings.value?.settings?.title ?? 'Family Tribute')

const colorMode = useColorMode()
function toggleTheme() {
  colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
}

const navLinks = [
  { label: 'People', to: '/people' },
  { label: 'Pictures', to: '/pictures' },
  { label: 'Stories', to: '/stories' },
]
</script>

<template>
  <div class="min-h-screen bg-hearthlight-subtle/40 font-sans text-inkwell dark:bg-inkwell dark:text-aged-edge">
    <header class="sticky top-0 z-10 bg-white/90 shadow-surface backdrop-blur dark:bg-inkwell/90">
      <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3">
        <NuxtLink to="/" class="font-header text-4xl leading-none text-hearthlight-deep dark:text-hearthlight">
          {{ siteTitle }}
        </NuxtLink>

        <nav role="navigation" class="flex items-center gap-6">
          <NuxtLink
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            class="text-faded-ink transition-colors hover:text-hearthlight-deep dark:text-aged-edge dark:hover:text-hearthlight"
          >
            {{ link.label }}
          </NuxtLink>
          <button
            type="button"
            aria-label="Toggle dark mode"
            class="text-faded-ink transition-colors hover:text-hearthlight-deep dark:text-aged-edge dark:hover:text-hearthlight"
            @click="toggleTheme"
          >
            <ClientOnly>
              <i :class="colorMode.value === 'dark' ? 'ri-sun-line' : 'ri-moon-line'"/>
              <template #fallback><i class="ri-contrast-2-line"/></template>
            </ClientOnly>
          </button>
        </nav>
      </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10">
      <slot />
    </main>

    <footer class="mx-auto max-w-5xl px-4 py-10 text-center text-sm text-old-binding">
      <p>{{ siteTitle }}</p>
    </footer>
  </div>
</template>
