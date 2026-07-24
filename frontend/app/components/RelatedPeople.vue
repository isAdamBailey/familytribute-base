<script setup lang="ts">
import type { PersonRef } from '~/types/api'

defineProps<{ title: string, people: PersonRef[] }>()
</script>

<template>
  <section v-if="people.length" class="mt-10">
    <h2 class="font-header text-3xl text-hearthlight-deep dark:text-hearthlight">{{ title }}</h2>
    <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
      <NuxtLink
        v-for="ref in people"
        :key="ref.slug"
        :to="`/${ref.slug}`"
        class="flex flex-col items-center text-center"
      >
        <img
          :src="ref.photo_url"
          :alt="ref.full_name"
          class="aspect-square w-full rounded-full object-cover shadow-card"
          loading="lazy"
        >
        <p class="mt-2 text-sm font-semibold text-inkwell dark:text-aged-edge">{{ ref.full_name }}</p>
      </NuxtLink>
    </div>
  </section>
</template>
