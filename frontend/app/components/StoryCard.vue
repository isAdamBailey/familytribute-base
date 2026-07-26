<script setup lang="ts">
import type { Story } from '~/types/api'

defineProps<{ story: Story }>()
</script>

<template>
  <NuxtLink
    :to="`/stories/${story.slug}`"
    class="block rounded-lg bg-white p-4 shadow-card transition hover:shadow-card-hover dark:bg-inkwell/60"
  >
    <div class="flex items-baseline justify-between gap-2">
      <p class="font-semibold text-inkwell dark:text-aged-edge">{{ story.title }}</p>
      <span v-if="story.year" class="shrink-0 text-sm text-faded-ink dark:text-old-binding">{{ story.year }}</span>
    </div>
    <div class="html-content mt-2 line-clamp-3 text-sm text-faded-ink dark:text-old-binding" v-html="story.excerpt" />
  </NuxtLink>
</template>
