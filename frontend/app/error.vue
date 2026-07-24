<script setup lang="ts">
import type { NuxtError } from '#app'

const props = defineProps<{ error: NuxtError }>()

const isNotFound = computed(() => props.error?.statusCode === 404)
const heading = computed(() =>
  isNotFound.value ? '404: Page Not Found' : `${props.error?.statusCode ?? 'Error'}: Something went wrong`,
)

useSeoMeta({ title: () => heading.value })

function handleBackHome() {
  clearError({ redirect: '/' })
}
</script>

<template>
  <div class="flex min-h-screen flex-col items-center justify-center bg-hearthlight-subtle/40 px-4 text-center font-sans text-inkwell dark:bg-inkwell dark:text-aged-edge">
    <h1 class="font-header text-5xl text-hearthlight-deep dark:text-hearthlight">
      {{ heading }}
    </h1>
    <p class="mt-4 max-w-md text-faded-ink dark:text-old-binding">
      The page you were looking for could not be found.
    </p>
    <a
      href="/"
      class="mt-8 rounded-md bg-hearthlight px-6 py-3 font-semibold text-white shadow-card transition hover:bg-hearthlight-deep hover:shadow-button-hover"
      @click.prevent="handleBackHome"
    >
      Back Home
    </a>
  </div>
</template>
