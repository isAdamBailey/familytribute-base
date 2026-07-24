<script setup lang="ts">
import type { Picture } from '~/types/api'

defineProps<{ picture: Picture }>()
</script>

<template>
  <NuxtLink
    :to="`/pictures/${picture.slug}`"
    class="block overflow-hidden rounded-lg bg-white shadow-card transition hover:shadow-card-hover dark:bg-inkwell/60"
  >
    <img
      :src="picture.url"
      :alt="picture.title"
      class="aspect-[4/3] w-full object-cover"
      loading="lazy"
    >
    <div class="card-text-gradient px-4 py-3 dark:bg-none">
      <p class="font-semibold text-inkwell dark:text-aged-edge">{{ picture.title }}</p>
      <p class="mt-1 text-sm text-faded-ink dark:text-old-binding">{{ picture.year }}</p>
    </div>
  </NuxtLink>
</template>
