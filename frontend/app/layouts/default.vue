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

const { isLoggedIn } = useAuth()
const currentYear = new Date().getFullYear()

// Nuxt doesn't have its own Login/Register pages yet (issue #19 Phase 5).
// Link straight to the still-live Inertia/Fortify auth pages on the backend
// origin so these links work today instead of 404ing against a Nuxt route
// that doesn't exist; swap to NuxtLink once Phase 5 ships.
const config = useRuntimeConfig()
const loginUrl = computed(() => `${config.public.backendOrigin}/login`)
const registerUrl = computed(() => `${config.public.backendOrigin}/register`)
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

    <footer class="border-t border-hearthlight-subtle px-4 py-10 dark:border-old-binding/30">
      <div class="mx-auto flex max-w-5xl flex-col items-center gap-6 text-center">
        <div v-if="!isLoggedIn" class="flex items-center gap-4">
          <a :href="loginUrl" class="text-inkwell underline hover:text-hearthlight-deep dark:text-aged-edge dark:hover:text-hearthlight">
            Log In
          </a>
          <a
            v-if="settings?.settings?.registration"
            :href="registerUrl"
            class="text-inkwell underline hover:text-hearthlight-deep dark:text-aged-edge dark:hover:text-hearthlight"
          >
            Register
          </a>
        </div>
        <p class="border-t border-hearthlight-subtle pt-6 text-sm font-bold text-hearthlight-deep dark:border-old-binding/30 dark:text-hearthlight">
          © {{ currentYear }} {{ siteTitle }}
        </p>
      </div>
    </footer>
  </div>
</template>
