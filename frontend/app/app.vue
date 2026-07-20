<script setup lang="ts">
// Global default meta; per-page useSeoMeta overrides title/description/OG.
useHead({
  titleTemplate: (title) => (title ? title : 'Family Tribute'),
})
</script>

<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>
