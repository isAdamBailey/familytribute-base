<script setup lang="ts">
import type { Person } from '~/types/api'

defineProps<{ person: Person }>()
</script>

<template>
  <NuxtLink
    :to="`/${person.slug}`"
    class="block rounded-lg bg-white shadow-card transition hover:-translate-y-0.5 hover:shadow-card-hover dark:bg-inkwell/60"
  >
    <img
      :src="person.photo_url"
      :alt="person.full_name"
      class="h-48 w-full rounded-t-lg object-cover"
      loading="lazy"
    >
    <div class="card-text-gradient rounded-b-lg px-3 py-2 dark:bg-none">
      <p class="truncate font-bold text-inkwell dark:text-aged-edge">{{ person.full_name }}</p>
      <p v-if="person.obituary" class="truncate text-xs font-semibold text-faded-ink dark:text-old-binding">
        {{ formatDate(person.obituary.birth_date) }} – {{ formatDate(person.obituary.death_date) }}
      </p>
    </div>
  </NuxtLink>
</template>
