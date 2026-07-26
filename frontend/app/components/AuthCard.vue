<script setup lang="ts">
defineProps<{ title: string }>()
</script>

<template>
  <div class="mx-auto max-w-md rounded-md bg-white p-8 shadow-card dark:bg-inkwell">
    <h1 class="font-header text-4xl text-hearthlight-deep dark:text-hearthlight">
      {{ title }}
    </h1>
    <slot />
  </div>
</template>
