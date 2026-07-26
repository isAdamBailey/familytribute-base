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

const { user, isLoggedIn, logout } = useAuth()
const currentYear = new Date().getFullYear()

const userMenuOpen = ref(false)
async function handleLogout() {
  userMenuOpen.value = false
  await logout()
  await navigateTo('/')
}
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

          <div v-if="isLoggedIn" class="relative">
            <button
              type="button"
              class="flex rounded-full border-2 border-transparent transition focus:border-hearthlight/50 focus:outline-none"
              @click="userMenuOpen = !userMenuOpen"
            >
              <img
                :src="user?.profile_photo_url ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name ?? '')}&color=bf8028&background=fbeed9`"
                :alt="user?.name"
                class="h-8 w-8 rounded-full object-cover"
              >
            </button>

            <div
              v-if="userMenuOpen"
              class="absolute right-0 mt-2 w-48 rounded-md border border-hearthlight-subtle bg-white py-1 shadow-card dark:border-old-binding/30 dark:bg-inkwell"
            >
              <NuxtLink
                to="/dashboard"
                class="block px-4 py-2 text-sm text-inkwell hover:bg-hearthlight-subtle/40 dark:text-aged-edge dark:hover:bg-old-binding/10"
                @click="userMenuOpen = false"
              >
                Dashboard
              </NuxtLink>
              <NuxtLink
                to="/user/profile"
                class="block px-4 py-2 text-sm text-inkwell hover:bg-hearthlight-subtle/40 dark:text-aged-edge dark:hover:bg-old-binding/10"
                @click="userMenuOpen = false"
              >
                Profile
              </NuxtLink>
              <button
                type="button"
                class="block w-full px-4 py-2 text-left text-sm text-inkwell hover:bg-hearthlight-subtle/40 dark:text-aged-edge dark:hover:bg-old-binding/10"
                @click="handleLogout"
              >
                Log Out
              </button>
            </div>
          </div>
        </nav>
      </div>
    </header>

    <FlashBanner />

    <main class="mx-auto max-w-5xl px-4 py-10">
      <slot />
    </main>

    <footer class="border-t border-hearthlight-subtle px-4 py-10 dark:border-old-binding/30">
      <div class="mx-auto flex max-w-5xl flex-col items-center gap-6 text-center">
        <div v-if="!isLoggedIn" class="flex items-center gap-4">
          <NuxtLink to="/login" class="text-inkwell underline hover:text-hearthlight-deep dark:text-aged-edge dark:hover:text-hearthlight">
            Log In
          </NuxtLink>
          <NuxtLink
            v-if="settings?.settings?.registration"
            to="/register"
            class="text-inkwell underline hover:text-hearthlight-deep dark:text-aged-edge dark:hover:text-hearthlight"
          >
            Register
          </NuxtLink>
        </div>
        <p class="border-t border-hearthlight-subtle pt-6 text-sm font-bold text-hearthlight-deep dark:border-old-binding/30 dark:text-hearthlight">
          © {{ currentYear }} {{ siteTitle }}
        </p>
      </div>
    </footer>
  </div>
</template>
